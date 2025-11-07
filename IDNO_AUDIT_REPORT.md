# Database Relationship Audit Report
## `idno` vs `reference` Usage Analysis

**Date:** 2025
**Project:** GloryServant v2

---

## Executive Summary

Your codebase uses **BOTH** `idno` (VARCHAR) and `reference` (INT) for relationships:
- **48 instances** of `where('idno', ...)` in controllers
- **60 instances** of `where('reference', ...)` in controllers
- **Mixed usage** creates data integrity risks

---

## Critical Issues Found

### 1. **JOIN Operations Using `idno` (High Risk)**

#### File: `DevotionPublicController.php`
**Line 61:**
```php
->leftJoin('tbl_campus_data as cd', function($join){
    $join->on('cd.idno', '=', 'd.idno')  // ❌ JOINING ON idno
         ->on('cd.reference', '=', 'p.id');
})
```
**Issue:** Joining on `idno` (string) instead of `reference` (int)

**Line 250:**
```php
$person = DB::table('tbl_campus_data as cd')
    ->join('tbl_people as p', 'p.id', '=', 'cd.reference')
    ->where('cd.idno', $idno)  // ❌ WHERE on idno
```

---

#### File: `DashboardController.php`
**Line 377:**
```php
$join->on('pa.idno', '=', 'recent.idno')  // ❌ JOINING ON idno
```

**Line 713-715:**
```php
// 4a) join to get each person's idno
->leftJoin('tbl_campus_data AS cd', 'p.id', '=', 'cd.reference')
// 4b) join counts on cd.idno  // ❌ Then using idno for subquery joins
->leftJoinSub($devotions, 'dev', fn($j) => $j->on('dev.idno','cd.idno'))
```

---

### 2. **WHERE Clauses Using `idno` (Medium Risk)**

#### Personal Controllers (User-facing queries)
```php
// PersonalLeavesController.php:30
$l = table::leaves()->where('idno', $i)->get();  // ❌

// PersonalAttendanceController.php:22
$a = table::attendance()->where('idno', $i)->get();  // ❌

// PersonalSchedulesController.php:23
$s = table::schedules()->where('idno', $i)->get();  // ❌
```

#### Admin Controllers (Reports & Exports)
```php
// ReportsController.php:256
$data = table::attendance()->where('idno', $id)->get();  // ❌

// ExportsController.php:159
$data = table::attendance()->where('idno', $id)->whereBetween(...)->get();  // ❌

// SchedulesController.php:317
table::schedules()->where('idno', $idno)->update(['is_active' => 0]);  // ❌
```

---

### 3. **Mixed Usage in Same File**

#### File: `UsersController.php`
```php
// Line: Gets idno from reference
$idno = table::campusdata()->where('reference', $ref)->value('idno');  // ✅ Uses reference
// Then uses idno elsewhere
```

#### File: `SchedulesController.php`
```php
// Gets idno from reference
$idno = table::campusdata()->where('reference', $reference)->value('idno');  // ✅
// Then filters by idno
table::schedules()->where('idno', $idno)->update(...);  // ❌
```

---

## Impact Analysis

### **What Happens When `idno` Changes:**

**Scenario:** Employee "John Doe" gets new badge number
- `tbl_people.id` = 123 (never changes)
- `idno` changes from "EMP001" → "EMP999"

**Current Behavior:**
1. Update `tbl_people_attendance.idno` = "EMP999"
2. ❌ Old attendance records still have "EMP001"
3. ❌ Queries using `where('idno', ...)` won't find old records
4. ❌ Reports show incomplete data
5. ❌ Personal dashboards break

**If Using `reference`:**
1. Update `tbl_people.idno` = "EMP999" (one place)
2. ✅ All queries using `reference` still work
3. ✅ Complete history maintained
4. ✅ No broken relationships

---

## Recommendations

### **Priority 1: Fix JOIN Operations (Critical)**
Replace all `idno` joins with `reference` joins:

```php
// BEFORE (❌ Bad)
->leftJoin('tbl_campus_data as cd', function($join){
    $join->on('cd.idno', '=', 'd.idno');
})

// AFTER (✅ Good)
->leftJoin('tbl_campus_data as cd', function($join){
    $join->on('cd.reference', '=', 'd.reference');
})
```

### **Priority 2: Standardize WHERE Clauses (High)**
Use `reference` for all user-scoped queries:

```php
// BEFORE (❌ Bad)
$i = \Auth::user()->idno;
$a = table::attendance()->where('idno', $i)->get();

// AFTER (✅ Good)
$ref = \Auth::user()->reference;
$a = table::attendance()->where('reference', $ref)->get();
```

### **Priority 3: Add Foreign Keys (Medium)**
Enforce data integrity:

```sql
ALTER TABLE users 
ADD CONSTRAINT fk_users_reference 
FOREIGN KEY (reference) REFERENCES tbl_people(id) 
ON DELETE CASCADE;

ALTER TABLE tbl_campus_data 
ADD CONSTRAINT fk_campus_data_reference 
FOREIGN KEY (reference) REFERENCES tbl_people(id) 
ON DELETE CASCADE;

-- Repeat for all tables with reference column
```

---

## Files Requiring Changes

### **High Priority (JOINs):**
1. `app/Http/Controllers/DevotionPublicController.php` (lines 61, 250)
2. `app/Http/Controllers/Admin/DashboardController.php` (lines 377, 713-715)

### **Medium Priority (WHERE clauses):**
3. `app/Http/Controllers/Personal/PersonalLeavesController.php`
4. `app/Http/Controllers/Personal/PersonalAttendanceController.php`
5. `app/Http/Controllers/Personal/PersonalSchedulesController.php`
6. `app/Http/Controllers/Admin/ReportsController.php`
7. `app/Http/Controllers/Admin/ExportsController.php`
8. `app/Http/Controllers/Admin/SchedulesController.php`
9. `app/Http/Controllers/Admin/EmployeesController.php`
10. `app/Http/Controllers/Admin/ProjectController.php`

---

## Migration Strategy

### **Phase 1: Audit (Current)**
✅ Identify all `idno` usage
✅ Document impact

### **Phase 2: Add Reference Columns (If Missing)**
- Ensure all data tables have `reference` column
- Populate from `tbl_campus_data` where missing

### **Phase 3: Update Code Gradually**
- Start with JOIN operations (highest risk)
- Then WHERE clauses in admin controllers
- Finally personal controllers
- Test each change thoroughly

### **Phase 4: Add Foreign Keys**
- Once code is updated
- Add constraints for data integrity

### **Phase 5: Keep `idno` for Display Only**
- Use `idno` for badges, reports, UI
- Never for relationships or queries

---

## Data Access Layer Impact

**For your data scoping implementation:**

### **Option A: Scope by Campus/Ministry/Dept (Recommended)**
- Avoids the `reference` vs `idno` issue entirely
- Filter by organizational units
- No code changes needed

### **Option B: Scope by User Reference**
- Use `reference` (not `idno`) for user identification
- Requires fixing the issues above first
- More precise user-level scoping

---

## Summary Statistics

- **Total `idno` WHERE clauses:** 48
- **Total `reference` WHERE clauses:** 60
- **JOIN operations using `idno`:** 3 (critical)
- **Files affected:** ~10 controllers
- **Estimated effort:** 2-3 days to fix all issues

---

## Next Steps

1. **Decide:** Fix `idno` issues OR implement campus-based scoping?
2. **If fixing:** Start with JOIN operations in DevotionPublicController and DashboardController
3. **If scoping:** Proceed with campus/ministry/dept filtering (safer, no code changes)

---

**End of Report**
