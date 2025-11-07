# INSERT/UPDATE Operations Audit
## Using `idno` Instead of `reference`

**Date:** 2025-01-XX  
**Status:** 🔴 CRITICAL DATA INTEGRITY ISSUES FOUND

---

## Executive Summary

**Problem:** Controllers are INSERTing new records with `idno` (VARCHAR badge number) instead of `reference` (INT person ID). When badge numbers change, historical data becomes orphaned.

**Impact:** 
- New attendance/leave/devotion records can't be linked when badge changes
- Personal dashboards show incomplete history
- Reports exclude historical data
- Data integrity compromised

**Found:** 15+ INSERT operations across 8 controller files

---

## Critical INSERT Operations Using `idno`

### 1. **Personal/PersonalLeavesController.php** (Line 76-88)

**Current Code (❌ Broken):**
```php
$id = \Auth::user()->reference;      // ✅ Gets reference correctly
$idno = \Auth::user()->idno;         // ❌ Then gets idno

table::leaves()->insert([
    'reference' => $id,              // ✅ Stores reference
    'idno' => $idno,                 // ⚠️ Also stores idno (redundant)
    'employee' => $q->lastname.', '.$q->firstname,
    'type' => $type,
    // ...
]);
```

**Status:** ⚠️ PARTIALLY CORRECT
- Stores BOTH `reference` and `idno`
- `reference` is correct, so queries work
- `idno` is redundant but not harmful

**Fix Priority:** LOW (already has reference)

---

### 2. **ClockController.php** (Lines 138, 216)

**Current Code (❌ BROKEN):**
```php
// Line 29: Gets idno from Auth
$idno = \Auth::user()->idno;  // ❌ Should use reference

// Line 138: INSERT with idno
table::attendance()->insert([[
    'idno'          => $idno,        // ❌ Uses idno
    'reference'     => $user->id,    // ✅ Has reference too
    'date'          => $date,
    'employee'      => $employee,
    'timein'        => "$date $timeRaw",
    // ...
]]);

// Line 216: QR scan also uses idno
$request->merge([
    'idno' => Auth::user()->idno,    // ❌ Should use reference
    'type' => $action,
]);
```

**Status:** ⚠️ PARTIALLY CORRECT
- Stores BOTH `idno` and `reference`
- `reference` is correct
- But initial lookup uses `idno` which could fail

**Issue:** Lines 75-80 lookup user by idno:
```php
$user = User::whereRaw('UPPER(idno)=?', [strtoupper($data['idno'])])->first();
if (!$user) {
    return $this->jsonError('You entered an invalid ID.', 422);
}
```

**Fix Priority:** MEDIUM (has reference but lookup uses idno)

---

### 3. **DevotionController.php** (Line 42)

**Current Code (❌ BROKEN):**
```php
$id = \Auth::user()->reference;      // ✅ Gets reference
$idno = \Auth::user()->idno;         // ❌ Gets idno

DB::table('tbl_people_devotion')->insert([
    'devotion_date' => $request->devotion_date,
    'devotion_text' => $request->devotion_text,
    'reference'     => $id,          // ✅ Stores reference
    'idno'          => $idno,        // ⚠️ Also stores idno (redundant)
    'employee'      => $employee,
    // ...
]);
```

**Status:** ⚠️ PARTIALLY CORRECT
- Has `reference` column
- `idno` is redundant

**Fix Priority:** LOW (already has reference)

---

### 4. **Admin/AttendanceController.php** (Lines 210, 278)

**Current Code (❌ BROKEN):**
```php
// Line 159: Gets idno from campus_data
$emp_idno = table::campusdata()->where('id', $reference)->value('idno');

// Line 210: INSERT with idno
table::attendance()->insert([[
    'idno' => $emp_idno,             // ❌ Uses idno
    'reference' => $emp_id,          // ✅ Has reference too
    'date' => $date,
    'employee' => $employee,
    'timein' => $date." ".$timein,
    // ...
]]);
```

**Status:** ⚠️ PARTIALLY CORRECT
- Stores BOTH `idno` and `reference`
- `reference` is correct

**Fix Priority:** LOW (already has reference)

---

### 5. **Admin/SchedulesController.php** (Lines 190, 317)

**Current Code (❌ BROKEN):**
```php
// Line 145: Gets idno from campus_data
$idno = table::campusdata()->where('reference', $reference)->value('idno');

// Line 190: INSERT with idno
table::schedules()->insert([
    'reference' => $reference,       // ✅ Has reference
    'idno'      => $idno,            // ⚠️ Also stores idno
    'employee'  => $employeeName,
    // ...
]);

// Line 317: UPDATE using idno ❌ CRITICAL
table::schedules()->where('idno', $idno)->update(['is_active' => 0]);
```

**Status:** 🔴 CRITICAL
- INSERT is OK (has reference)
- **UPDATE on line 317 uses `idno` in WHERE clause** ❌
- When badge changes, UPDATE won't find old schedules

**Fix Priority:** HIGH (UPDATE uses idno)

---

### 6. **DevotionPublicController.php** (Line 272)

**Current Code (❌ BROKEN):**
```php
// Line 232: Validates idno from form
'idno' => ['required','string','max:50'],

// Line 250: Looks up person by idno
$person = DB::table('tbl_campus_data as cd')
    ->join('tbl_people as p', 'p.id', '=', 'cd.reference')
    ->where('cd.idno', $idno)  // ❌ WHERE on idno
    ->first();

// Line 272: INSERT with idno
DB::table('tbl_people_devotion')->insert([
    'idno'           => $idno,       // ❌ Uses idno
    'reference'      => $person->id, // ✅ Has reference too
    'employee'       => $employee,
    // ...
]);
```

**Status:** ⚠️ PARTIALLY CORRECT
- Has `reference` column
- But lookup uses `idno` which could fail for old badge numbers

**Fix Priority:** MEDIUM (lookup uses idno)

---

### 7. **Personal/PersonalAttendanceController.php** (Lines 21, 30, 38)

**Current Code (❌ BROKEN):**
```php
// Line 21: Gets idno
$i = \Auth::user()->idno;  // ❌ Should use reference

// Line 22: WHERE using idno
$a = table::attendance()->where('idno', $i)->get();  // ❌

// Line 30: Gets idno again
$id = \Auth::user()->idno;  // ❌

// Line 38: WHERE using idno
->where('idno', $id)  // ❌
```

**Status:** 🔴 CRITICAL
- No INSERT, but all queries use `idno`
- When badge changes, user can't see their old attendance

**Fix Priority:** HIGH (queries broken)

---

### 8. **Personal/PersonalSchedulesController.php** (Lines 22, 31)

**Current Code (❌ BROKEN):**
```php
// Line 22: Gets idno
$i = \Auth::user()->idno;  // ❌

// Line 23: WHERE using idno
$s = table::schedules()->where('idno', $i)->get();  // ❌
```

**Status:** 🔴 CRITICAL
- Queries use `idno`
- User can't see schedules after badge change

**Fix Priority:** HIGH (queries broken)

---

## UPDATE Operations Using `idno`

### 1. **Admin/SchedulesController.php** (Line 317)

```php
// ❌ CRITICAL: Deactivates schedules by idno
table::schedules()->where('idno', $idno)->update(['is_active' => 0]);
```

**Problem:** When badge changes, this won't find old schedules to deactivate

**Fix:**
```php
// ✅ Use reference instead
table::schedules()->where('reference', $reference)->update(['is_active' => 0]);
```

---

## Summary Table

| File | Lines | Operation | Has Reference? | Priority | Issue |
|------|-------|-----------|----------------|----------|-------|
| PersonalLeavesController | 76-88 | INSERT | ✅ Yes | LOW | Redundant idno |
| ClockController | 138 | INSERT | ✅ Yes | MEDIUM | Lookup uses idno |
| DevotionController | 42 | INSERT | ✅ Yes | LOW | Redundant idno |
| AttendanceController | 210, 278 | INSERT | ✅ Yes | LOW | Redundant idno |
| SchedulesController | 190 | INSERT | ✅ Yes | LOW | Redundant idno |
| SchedulesController | 317 | UPDATE | ✅ Yes | 🔴 HIGH | WHERE uses idno |
| DevotionPublicController | 272 | INSERT | ✅ Yes | MEDIUM | Lookup uses idno |
| PersonalAttendanceController | 21-38 | SELECT | ❌ No | 🔴 HIGH | WHERE uses idno |
| PersonalSchedulesController | 22-31 | SELECT | ❌ No | 🔴 HIGH | WHERE uses idno |

---

## Good News 🎉

**Most INSERT operations already store `reference`!**

The main issues are:
1. **WHERE clauses** using `idno` instead of `reference` (Personal controllers)
2. **UPDATE operations** using `idno` in WHERE clause (SchedulesController line 317)
3. **Lookups** using `idno` before INSERT (ClockController, DevotionPublicController)

---

## Recommended Fixes

### **Priority 1: Fix UPDATE Operations (CRITICAL)**

**File:** `Admin/SchedulesController.php` Line 317

```php
// BEFORE ❌
table::schedules()->where('idno', $idno)->update(['is_active' => 0]);

// AFTER ✅
table::schedules()->where('reference', $reference)->update(['is_active' => 0]);
```

**Impact:** Prevents schedules from being orphaned when badge changes

---

### **Priority 2: Fix Personal Controller WHERE Clauses (HIGH)**

**Files:**
- `Personal/PersonalAttendanceController.php`
- `Personal/PersonalSchedulesController.php`
- `Personal/PersonalLeavesController.php` (line 30)

**Pattern:**
```php
// BEFORE ❌
$i = \Auth::user()->idno;
$data = table::attendance()->where('idno', $i)->get();

// AFTER ✅
$ref = \Auth::user()->reference;
$data = table::attendance()->where('reference', $ref)->get();
```

**Impact:** Users can see their full history after badge changes

---

### **Priority 3: Fix Lookups (MEDIUM)**

**Files:**
- `ClockController.php` (line 75)
- `DevotionPublicController.php` (line 250)

**Pattern:**
```php
// BEFORE ❌
$user = User::whereRaw('UPPER(idno)=?', [strtoupper($idno)])->first();

// AFTER ✅
// Keep idno for initial input, but also support reference
$user = User::whereRaw('UPPER(idno)=?', [strtoupper($idno)])
    ->orWhere('reference', $idno)  // fallback if numeric
    ->first();
```

---

### **Priority 4: Remove Redundant `idno` from INSERTs (LOW)**

Since most tables already have `reference`, the `idno` column is redundant. Consider:

**Option A:** Keep both for backward compatibility
**Option B:** Stop inserting `idno`, only use `reference`

**Recommendation:** Keep both for now, remove `idno` in future major version

---

## Testing Checklist

After fixes:

- [ ] Change an employee's badge number (idno)
- [ ] Verify they can still see old attendance records
- [ ] Verify they can still see old leave requests
- [ ] Verify they can still see old schedules
- [ ] Verify schedule activation/deactivation works
- [ ] Verify clock in/out still works
- [ ] Verify devotion submission works

---

## Architecture Decision

### **Standard Pattern Going Forward:**

```php
// ✅ CORRECT: Always use reference
$ref = Auth::user()->reference;

// INSERT
table::something()->insert([
    'reference' => $ref,     // ✅ Required
    'idno' => Auth::user()->idno,  // ⚠️ Optional (for display only)
    // ...
]);

// SELECT
$data = table::something()->where('reference', $ref)->get();

// UPDATE
table::something()->where('reference', $ref)->update([...]);

// DELETE
table::something()->where('reference', $ref)->delete();
```

### **Never Do This:**

```php
// ❌ WRONG: Don't use idno for relationships
$idno = Auth::user()->idno;
table::something()->where('idno', $idno)->get();
```

---

## Estimated Fix Time

| Priority | Files | Operations | Time |
|----------|-------|------------|------|
| HIGH | 3 files | 1 UPDATE + 6 WHERE | 45 min |
| MEDIUM | 2 files | 2 lookups | 30 min |
| LOW | 5 files | Remove redundant idno | 1 hour |
| **TOTAL** | **10 files** | **~15 operations** | **2-3 hours** |

---

## Conclusion

**Good News:** Most INSERT operations already store `reference` ✅

**Bad News:** Many WHERE/UPDATE operations still use `idno` ❌

**Action Required:** Fix HIGH priority WHERE clauses and UPDATE operations to use `reference` instead of `idno`

---

**End of Audit**

