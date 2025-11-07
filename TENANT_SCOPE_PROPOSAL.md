# Tenant/Scope-Based Data Filtering Proposal
## Simplest Implementation Strategy

**Date:** 2025-01-10  
**Goal:** Restrict user access to data based on organizational units (Campus/Ministry/Department)

---

## Current System Analysis

### **Database Structure:**

```
users
  ├─ reference → tbl_people.id
  └─ (no scope columns)

tbl_campus_data (organizational assignment)
  ├─ reference → tbl_people.id
  ├─ campus (VARCHAR)
  ├─ campus_id (INT)
  ├─ ministry (VARCHAR)
  └─ department (VARCHAR)

Data Tables (all have reference column):
  ├─ tbl_people_attendance.reference
  ├─ tbl_people_leaves.reference
  ├─ tbl_people_schedules.reference
  └─ tbl_people_devotion.reference
```

### **Key Insight:**

✅ All data tables already have `reference` column  
✅ `tbl_campus_data` links people to organizational units  
✅ Users can be scoped via `users.reference → tbl_campus_data`

---

## Recommended Solution: **Global Scope Trait**

### **Why This Approach:**

1. ✅ **Minimal Code Changes** - Add trait to models, done
2. ✅ **Automatic Filtering** - Works on all queries automatically
3. ✅ **Centralized Logic** - One place to manage scoping
4. ✅ **Flexible** - Can disable per-query when needed
5. ✅ **Laravel Native** - Uses built-in Global Scopes

---

## Implementation Plan

### **Step 1: Add Scope Columns to Users Table**

```php
// Migration: Add scope columns to users
Schema::table('users', function (Blueprint $table) {
    $table->string('scope_campus')->nullable()->after('reference');
    $table->string('scope_ministry')->nullable()->after('scope_campus');
    $table->string('scope_department')->nullable()->after('scope_ministry');
    $table->boolean('scope_all_data')->default(false)->after('scope_department');
});
```

**Populate from tbl_campus_data:**
```sql
UPDATE users u
JOIN tbl_campus_data cd ON u.reference = cd.reference
SET 
    u.scope_campus = cd.campus,
    u.scope_ministry = cd.ministry,
    u.scope_department = cd.department;
```

---

### **Step 2: Create Tenant Scope Trait**

```php
// app/Traits/TenantScoped.php
namespace App\Traits;

use Illuminate\Database\Eloquent\Builder;

trait TenantScoped
{
    protected static function bootTenantScoped()
    {
        static::addGlobalScope('tenant', function (Builder $builder) {
            $user = auth()->user();
            
            // Skip if no user or user has access to all data
            if (!$user || $user->scope_all_data) {
                return;
            }

            // Get the table being queried
            $table = $builder->getModel()->getTable();
            
            // Apply scope based on reference column
            $builder->whereIn("{$table}.reference", function ($query) use ($user) {
                $query->select('reference')
                    ->from('tbl_campus_data')
                    ->when($user->scope_campus, fn($q) => $q->where('campus', $user->scope_campus))
                    ->when($user->scope_ministry, fn($q) => $q->where('ministry', $user->scope_ministry))
                    ->when($user->scope_department, fn($q) => $q->where('department', $user->scope_department));
            });
        });
    }
}
```

---

### **Step 3: Apply Trait to Models**

```php
// app/Models/Attendance.php
class Attendance extends Model
{
    use TenantScoped;
    protected $table = 'tbl_people_attendance';
}

// app/Models/Leave.php
class Leave extends Model
{
    use TenantScoped;
    protected $table = 'tbl_people_leaves';
}

// app/Models/Schedule.php
class Schedule extends Model
{
    use TenantScoped;
    protected $table = 'tbl_people_schedules';
}

// app/Models/Devotion.php
class Devotion extends Model
{
    use TenantScoped;
    protected $table = 'tbl_people_devotion';
}
```

---

### **Step 4: Update User Model**

```php
// app/Models/User.php
class User extends Authenticatable
{
    protected $fillable = [
        'name', 'email', 'password', 'reference',
        'scope_campus', 'scope_ministry', 'scope_department', 'scope_all_data'
    ];

    protected $casts = [
        'scope_all_data' => 'boolean',
    ];

    // Helper to check if user has full access
    public function hasFullAccess(): bool
    {
        return $this->scope_all_data === true;
    }

    // Get user's organizational scope
    public function getScope(): array
    {
        return [
            'campus' => $this->scope_campus,
            'ministry' => $this->scope_ministry,
            'department' => $this->scope_department,
        ];
    }
}
```

---

## How It Works

### **Example 1: User with Campus Scope**

```php
// User: campus = "TG DELMAS", ministry = null, department = null

// This query automatically filters:
$attendance = Attendance::all();

// Becomes:
SELECT * FROM tbl_people_attendance
WHERE reference IN (
    SELECT reference FROM tbl_campus_data WHERE campus = 'TG DELMAS'
);
```

### **Example 2: User with Ministry Scope**

```php
// User: campus = "TG DELMAS", ministry = "Communication", department = null

$leaves = Leave::all();

// Becomes:
SELECT * FROM tbl_people_leaves
WHERE reference IN (
    SELECT reference FROM tbl_campus_data 
    WHERE campus = 'TG DELMAS' AND ministry = 'Communication'
);
```

### **Example 3: Admin with Full Access**

```php
// User: scope_all_data = true

$attendance = Attendance::all();

// No filtering applied - sees all data
SELECT * FROM tbl_people_attendance;
```

---

## Bypassing Scope (When Needed)

```php
// Temporarily disable scope for specific query
$allAttendance = Attendance::withoutGlobalScope('tenant')->get();

// Or disable all scopes
$allAttendance = Attendance::withoutGlobalScopes()->get();
```

---

## Migration Files Needed

### **1. Add Scope Columns**
```bash
php artisan make:migration add_scope_columns_to_users_table
```

### **2. Populate Scope Data**
```bash
php artisan make:migration populate_user_scope_data
```

---

## User Interface Changes

### **Admin Panel: User Management**

Add fields to user edit form:
- **Scope Campus** (dropdown)
- **Scope Ministry** (dropdown)
- **Scope Department** (dropdown)
- **Full Data Access** (checkbox)

```blade
<div class="form-group">
    <label>Data Access Scope</label>
    <select name="scope_campus" class="form-control">
        <option value="">All Campuses</option>
        @foreach($campuses as $campus)
            <option value="{{ $campus }}">{{ $campus }}</option>
        @endforeach
    </select>
</div>

<div class="form-check">
    <input type="checkbox" name="scope_all_data" value="1">
    <label>Grant Full Data Access (Admin)</label>
</div>
```

---

## Testing Checklist

- [ ] Create test user with campus scope
- [ ] Verify they only see their campus data
- [ ] Create test user with ministry scope
- [ ] Verify they only see their ministry data
- [ ] Create admin user with full access
- [ ] Verify they see all data
- [ ] Test reports and exports respect scope
- [ ] Test personal dashboards work correctly

---

## Advantages of This Approach

✅ **Automatic** - No manual WHERE clauses needed  
✅ **Secure** - Can't be bypassed accidentally  
✅ **Flexible** - Easy to add/remove scope per user  
✅ **Maintainable** - One trait, applied everywhere  
✅ **Performant** - Uses indexed reference column  
✅ **Granular** - Campus, Ministry, or Department level  

---

## Alternative: Middleware Approach (Not Recommended)

```php
// Less elegant, requires manual filtering in every controller
public function index()
{
    $user = auth()->user();
    $query = Attendance::query();
    
    if (!$user->scope_all_data) {
        $query->whereIn('reference', function($q) use ($user) {
            // ... manual filtering
        });
    }
    
    $data = $query->get();
}
```

**Why Not:** Requires changes in 50+ controller methods

---

## Estimated Implementation Time

| Task | Time |
|------|------|
| Create migration | 15 min |
| Create TenantScoped trait | 30 min |
| Apply trait to 4 models | 10 min |
| Update User model | 15 min |
| Add UI for scope management | 1 hour |
| Testing | 2 hours |
| **Total** | **~4 hours** |

---

## Rollout Strategy

### **Phase 1: Setup (1 hour)**
1. Run migrations
2. Create trait
3. Apply to models

### **Phase 2: Testing (2 hours)**
1. Test with sample users
2. Verify data isolation
3. Check reports

### **Phase 3: Production (1 hour)**
1. Assign scopes to existing users
2. Monitor for issues
3. Adjust as needed

---

## Conclusion

**Recommended Solution:** Global Scope Trait

**Why:** Minimal code, maximum security, automatic filtering

**Next Step:** Create migration and trait, test with one model first

---

**Ready to implement?** Start with Step 1 migration.

