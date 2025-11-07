# Integrated Scope Solution
## Adding Data Scope to Existing Permission System

**Goal:** Add organizational scope (Campus/Ministry/Department) to your existing role-based permission system

---

## Current System

```
users_roles (Roles)
  ├─ ADMIN
  ├─ MANAGER
  ├─ CAMPUS POC
  ├─ CAMPUS ADMINISTRATOR
  ├─ MINISTRY LEADER
  └─ VOLUNTEER

permission::permitted('dashboard') → 'success' or 'fail'
```

---

## Proposed Enhancement

### **Add Scope Columns to `users_roles` Table**

```sql
ALTER TABLE users_roles 
ADD COLUMN scope_level ENUM('all', 'campus', 'ministry', 'department') DEFAULT 'all' AFTER state,
ADD COLUMN scope_description VARCHAR(255) NULL AFTER scope_level;
```

**Scope Levels:**
- `all` - See all data (Admin, Manager)
- `campus` - See only their campus data (Campus POC, Campus Admin)
- `ministry` - See only their ministry data (Ministry Leader)
- `department` - See only their department data (Department Head)

---

## Implementation

### **Step 1: Migration**

```php
// database/migrations/2025_01_10_add_scope_to_roles.php
public function up()
{
    Schema::table('users_roles', function (Blueprint $table) {
        $table->enum('scope_level', ['all', 'campus', 'ministry', 'department'])
              ->default('all')
              ->after('state');
        $table->string('scope_description')->nullable()->after('scope_level');
    });

    // Set scope for existing roles
    DB::table('users_roles')->where('role_name', 'ADMIN')->update(['scope_level' => 'all']);
    DB::table('users_roles')->where('role_name', 'MANAGER')->update(['scope_level' => 'all']);
    DB::table('users_roles')->where('role_name', 'CAMPUS POC')->update(['scope_level' => 'campus']);
    DB::table('users_roles')->where('role_name', 'CAMPUS ADMINISTRATOR')->update(['scope_level' => 'campus']);
    DB::table('users_roles')->where('role_name', 'MINISTRY LEADER')->update(['scope_level' => 'ministry']);
    DB::table('users_roles')->where('role_name', 'MINISTRY OVERSEER')->update(['scope_level' => 'ministry']);
}
```

---

### **Step 2: Enhanced Permission Class**

```php
// app/Classes/permission.php

public static function getScope(): ?array
{
    $user = auth()->user();
    if (!$user) return null;

    // Get role scope level
    $role = DB::table('users_roles')->where('id', $user->role_id)->first();
    if (!$role || $role->scope_level === 'all') {
        return null; // No filtering needed
    }

    // Get user's organizational assignment
    $campusData = DB::table('tbl_campus_data')
        ->where('reference', $user->reference)
        ->first();

    if (!$campusData) return null;

    // Return scope based on role level
    return match($role->scope_level) {
        'campus' => ['campus' => $campusData->campus],
        'ministry' => [
            'campus' => $campusData->campus,
            'ministry' => $campusData->ministry
        ],
        'department' => [
            'campus' => $campusData->campus,
            'ministry' => $campusData->ministry,
            'department' => $campusData->department
        ],
        default => null
    };
}

public static function hasFullAccess(): bool
{
    $user = auth()->user();
    if (!$user) return false;

    $role = DB::table('users_roles')->where('id', $user->role_id)->first();
    return $role && $role->scope_level === 'all';
}
```

---

### **Step 3: Scoped Trait (Simplified)**

```php
// app/Traits/TenantScoped.php
namespace App\Traits;

use Illuminate\Database\Eloquent\Builder;
use App\Classes\permission;

trait TenantScoped
{
    protected static function bootTenantScoped()
    {
        static::addGlobalScope('tenant', function (Builder $builder) {
            $scope = permission::getScope();
            
            if (!$scope) return; // No filtering for full access users

            $table = $builder->getModel()->getTable();
            
            $builder->whereIn("{$table}.reference", function ($query) use ($scope) {
                $query->select('reference')
                    ->from('tbl_campus_data')
                    ->when(isset($scope['campus']), fn($q) => $q->where('campus', $scope['campus']))
                    ->when(isset($scope['ministry']), fn($q) => $q->where('ministry', $scope['ministry']))
                    ->when(isset($scope['department']), fn($q) => $q->where('department', $scope['department']));
            });
        });
    }
}
```

---

### **Step 4: Apply to Models**

```php
// app/Models/Attendance.php
use App\Traits\TenantScoped;

class Attendance extends Model
{
    use TenantScoped;
    protected $table = 'tbl_people_attendance';
}

// Repeat for Leave, Schedule, Devotion models
```

---

## How It Works

### **Example 1: Admin User**

```php
// Role: ADMIN (scope_level = 'all')
permission::getScope(); // Returns null
Attendance::all(); // Returns ALL attendance records
```

### **Example 2: Campus POC**

```php
// Role: CAMPUS POC (scope_level = 'campus')
// User assigned to: campus = "TG DELMAS"

permission::getScope(); 
// Returns: ['campus' => 'TG DELMAS']

Attendance::all(); 
// Returns: Only attendance for TG DELMAS campus
```

### **Example 3: Ministry Leader**

```php
// Role: MINISTRY LEADER (scope_level = 'ministry')
// User assigned to: campus = "TG DELMAS", ministry = "Communication"

permission::getScope(); 
// Returns: ['campus' => 'TG DELMAS', 'ministry' => 'Communication']

Attendance::all(); 
// Returns: Only attendance for Communication ministry at TG DELMAS
```

---

## Role Configuration UI

### **Admin Panel: Edit Role**

```blade
<div class="form-group">
    <label>Data Access Scope</label>
    <select name="scope_level" class="form-control">
        <option value="all">All Data (Admin/Manager)</option>
        <option value="campus">Campus Level (Campus POC)</option>
        <option value="ministry">Ministry Level (Ministry Leader)</option>
        <option value="department">Department Level (Dept Head)</option>
    </select>
    <small class="text-muted">
        Determines what organizational data users with this role can see
    </small>
</div>
```

---

## Advantages of This Approach

✅ **Integrated** - Works with existing permission system  
✅ **Role-Based** - Scope defined at role level, not per-user  
✅ **Automatic** - No manual filtering needed  
✅ **Flexible** - Easy to change scope per role  
✅ **Minimal Code** - Only 3 new methods in permission class  
✅ **Backward Compatible** - Existing roles default to 'all'  

---

## Migration Path

### **Phase 1: Add Scope to Roles (30 min)**
```bash
php artisan make:migration add_scope_to_users_roles
php artisan migrate
```

### **Phase 2: Update Permission Class (30 min)**
Add `getScope()` and `hasFullAccess()` methods

### **Phase 3: Create Trait (15 min)**
Create `TenantScoped` trait

### **Phase 4: Apply to Models (15 min)**
Add trait to 4 models

### **Phase 5: Test (1 hour)**
- Test with different roles
- Verify data isolation
- Check reports

**Total Time: ~2.5 hours**

---

## Role Scope Mapping

| Role | Scope Level | Sees |
|------|-------------|------|
| ADMIN | all | Everything |
| MANAGER | all | Everything |
| OVERSEER | all | Everything |
| CAMPUS POC | campus | Their campus only |
| CAMPUS ADMINISTRATOR | campus | Their campus only |
| MINISTRY LEADER | ministry | Their ministry only |
| MINISTRY OVERSEER | ministry | Their ministry only |
| MINISTRY CORE TEAM | ministry | Their ministry only |
| VOLUNTEER | campus | Their campus only |

---

## Testing Checklist

- [ ] Admin sees all data
- [ ] Campus POC sees only their campus
- [ ] Ministry Leader sees only their ministry
- [ ] Reports respect scope
- [ ] Exports respect scope
- [ ] Personal dashboards work
- [ ] Can bypass scope with `withoutGlobalScope('tenant')`

---

## Comparison: Role-Based vs User-Based Scope

### **Role-Based (Recommended)**
```
✅ Simpler - Scope defined once per role
✅ Consistent - All users with same role have same scope
✅ Easier to manage - Change role, not individual users
✅ Fewer columns - No per-user scope columns needed
```

### **User-Based (Previous Proposal)**
```
⚠️ More complex - Scope per user
⚠️ More maintenance - Must set scope for each user
⚠️ More columns - Need scope columns on users table
✅ More flexible - Can override per user
```

---

## Conclusion

**Recommended:** Role-Based Scope Integration

**Why:** 
- Leverages existing permission system
- Simpler to implement and maintain
- Scope is a property of the role, not the user
- Aligns with your existing architecture

**Next Step:** Run migration to add scope columns to `users_roles` table

