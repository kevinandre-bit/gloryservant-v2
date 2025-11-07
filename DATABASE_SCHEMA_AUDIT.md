# Database Schema Audit Report
## `reference` vs `idno` Column Analysis

**Date:** 2025
**Project:** GloryServant v2
**Database:** u276774975_serveflow_db

---

## Executive Summary

**Tables Analyzed:** 80+
**Tables with `reference`:** 11
**Tables with `idno`:** 13
**Tables with BOTH:** 9
**Tables with ONLY `idno` (Missing `reference`):** 4 ⚠️

---

## Category 1: ✅ Tables with BOTH `reference` AND `idno` (Correct)

These tables have proper structure for relationships:

| Table | reference Type | idno Type | Status |
|-------|---------------|-----------|--------|
| `users` | INT(11) | VARCHAR(11) | ✅ Good |
| `tbl_campus_data` | INT(11) NOT NULL | VARCHAR(255) | ✅ Good |
| `tbl_people_attendance` | INT(11) | VARCHAR(11) | ✅ Good |
| `tbl_people_devotion` | INT(11) | VARCHAR(11) | ✅ Good |
| `tbl_people_leaves` | INT(11) | VARCHAR(11) | ✅ Good |
| `tbl_people_schedules` | INT(11) | VARCHAR(11) | ✅ Good |
| `requests` | INT(20) UNSIGNED | VARCHAR(50) | ✅ Good |
| `volunteer_tasks` | INT(11) | INT(11) | ⚠️ idno should be VARCHAR |
| `new_tbl_people_schedules` | VARCHAR(100) | N/A | ⚠️ reference should be INT |

**Notes:**
- These tables can properly link to `tbl_people.id` via `reference`
- `idno` is available for display/lookup purposes
- `volunteer_tasks.idno` is INT instead of VARCHAR (inconsistent)
- `new_tbl_people_schedules.reference` is VARCHAR instead of INT (wrong type)

---

## Category 2: ❌ Tables with ONLY `idno` (Missing `reference`)

**CRITICAL:** These tables should have `reference` column added:

### 1. `meeting_attendance` ⚠️ HIGH PRIORITY

**Current Structure:**
```sql
CREATE TABLE `meeting_attendance` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `user_id` bigint(20) UNSIGNED DEFAULT NULL,  -- Links to users.id
  `email` varchar(190) DEFAULT NULL,
  `idno` varchar(191) DEFAULT NULL,            -- ❌ Only idno, no reference
  `employee` varchar(190) DEFAULT NULL,
  `campus` varchar(100) DEFAULT NULL,
  `ministry` varchar(100) DEFAULT NULL,
  `dept` varchar(100) DEFAULT NULL,
  ...
)
```

**Issues:**
- Has `user_id` (links to `users.id`) but no `reference` (to `tbl_people.id`)
- Uses `idno` for employee identification
- If `idno` changes, historical records become orphaned
- Code joins on `idno` (see code audit)

**Recommended Fix:**
```sql
ALTER TABLE meeting_attendance 
ADD COLUMN reference INT(11) DEFAULT NULL AFTER user_id,
ADD INDEX idx_reference (reference);

-- Populate from users table
UPDATE meeting_attendance ma
JOIN users u ON ma.user_id = u.id
SET ma.reference = u.reference
WHERE ma.user_id IS NOT NULL;

-- Populate from tbl_campus_data for records without user_id
UPDATE meeting_attendance ma
JOIN tbl_campus_data cd ON ma.idno = cd.idno
SET ma.reference = cd.reference
WHERE ma.user_id IS NULL AND ma.idno IS NOT NULL;
```

---

### 2. `tbl_report_people` ⚠️ MEDIUM PRIORITY

**Current Structure:**
```sql
CREATE TABLE `tbl_report_people` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `first_name` varchar(120) NOT NULL,
  `last_name` varchar(120) NOT NULL,
  `idno` varchar(64) NOT NULL,               -- ❌ Only idno, no reference
  `team_id` bigint(20) UNSIGNED DEFAULT NULL,
  `status` enum('active','inactive') DEFAULT 'active',
  ...
)
```

**Issues:**
- Separate people table for reporting system
- Uses `idno` as unique identifier
- No link to main `tbl_people` table
- Duplicate person data (names stored here AND in `tbl_people`)

**Recommended Fix:**
```sql
ALTER TABLE tbl_report_people 
ADD COLUMN reference INT(11) DEFAULT NULL AFTER idno,
ADD UNIQUE INDEX idx_reference (reference);

-- Populate from tbl_campus_data
UPDATE tbl_report_people rp
JOIN tbl_campus_data cd ON rp.idno = cd.idno
SET rp.reference = cd.reference;

-- Add foreign key
ALTER TABLE tbl_report_people
ADD CONSTRAINT fk_report_people_reference 
FOREIGN KEY (reference) REFERENCES tbl_people(id) 
ON DELETE CASCADE;
```

**Better Solution:**
Consider removing `first_name` and `last_name` from this table and always join to `tbl_people` via `reference`.

---

### 3. `meetings` ⚠️ LOW PRIORITY

**Current Structure:**
```sql
CREATE TABLE `meetings` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `name` varchar(150) NOT NULL,
  `meeting_code` varchar(30) DEFAULT NULL,
  `slug` varchar(100) NOT NULL,
  `description` text DEFAULT NULL,
  ...
)
```

**Issues:**
- This is a meeting definition table (not person-related)
- Doesn't need `reference` or `idno`
- False positive in audit

**Action:** ✅ No changes needed

---

### 4. `tbl_report_metric_status_items` ⚠️ FALSE POSITIVE

**Current Structure:**
```sql
CREATE TABLE `tbl_report_metric_status_items` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `metric_id` bigint(20) UNSIGNED NOT NULL,
  `label` varchar(80) NOT NULL,
  `score` decimal(6,4) NOT NULL,
  ...
)
```

**Issues:**
- Configuration table (not person-related)
- Doesn't need `reference` or `idno`
- False positive in audit

**Action:** ✅ No changes needed

---

## Category 3: ⚠️ Tables with ONLY `reference` (No `idno`)

These tables have `reference` but no `idno` - this is usually fine:

| Table | Notes |
|-------|-------|
| `new_tbl_people_schedules` | Has `reference` (VARCHAR - wrong type!) |
| Others | Configuration/lookup tables - don't need idno |

**Issue with `new_tbl_people_schedules`:**
```sql
`reference` varchar(100) NOT NULL,  -- ❌ Should be INT(11)
```

**Fix:**
```sql
-- This requires careful migration since it's VARCHAR
-- 1. Add new column
ALTER TABLE new_tbl_people_schedules 
ADD COLUMN reference_int INT(11) DEFAULT NULL AFTER reference;

-- 2. Convert data
UPDATE new_tbl_people_schedules 
SET reference_int = CAST(reference AS UNSIGNED)
WHERE reference REGEXP '^[0-9]+$';

-- 3. Verify data
SELECT COUNT(*) FROM new_tbl_people_schedules WHERE reference_int IS NULL;

-- 4. Drop old, rename new (after verification)
-- ALTER TABLE new_tbl_people_schedules DROP COLUMN reference;
-- ALTER TABLE new_tbl_people_schedules CHANGE reference_int reference INT(11);
```

---

## Category 4: Data Type Inconsistencies

### `idno` Column Types Across Tables:

| Table | idno Type | Issue |
|-------|-----------|-------|
| `users` | VARCHAR(11) | ✅ Standard |
| `tbl_campus_data` | VARCHAR(255) | ⚠️ Too large |
| `tbl_people_attendance` | VARCHAR(11) | ✅ Standard |
| `tbl_people_devotion` | VARCHAR(11) | ✅ Standard |
| `tbl_people_leaves` | VARCHAR(11) | ✅ Standard |
| `tbl_people_schedules` | VARCHAR(11) | ✅ Standard |
| `meeting_attendance` | VARCHAR(191) | ⚠️ Too large |
| `tbl_report_people` | VARCHAR(64) | ⚠️ Different size |
| `requests` | VARCHAR(50) | ⚠️ Different size |
| `volunteer_tasks` | INT(11) | ❌ Wrong type! |

**Recommendation:** Standardize all `idno` columns to `VARCHAR(50)`

---

## Priority Action Items

### 🔴 CRITICAL (Do First)

1. **Add `reference` to `meeting_attendance`**
   - Most used table
   - Code heavily relies on it
   - Historical data at risk

2. **Fix `volunteer_tasks.idno` type**
   - Change from INT to VARCHAR
   - Align with other tables

### 🟡 HIGH PRIORITY

3. **Add `reference` to `tbl_report_people`**
   - Reporting system needs proper links
   - Duplicate data issue

4. **Fix `new_tbl_people_schedules.reference` type**
   - Change from VARCHAR to INT
   - Enable foreign keys

### 🟢 MEDIUM PRIORITY

5. **Standardize `idno` column sizes**
   - Make all VARCHAR(50)
   - Consistency across schema

6. **Add Foreign Key Constraints**
   - After fixing data types
   - Enforce referential integrity

---

## Migration Script

```sql
-- ============================================
-- CRITICAL FIXES
-- ============================================

-- 1. Add reference to meeting_attendance
ALTER TABLE meeting_attendance 
ADD COLUMN reference INT(11) DEFAULT NULL AFTER user_id,
ADD INDEX idx_reference (reference);

UPDATE meeting_attendance ma
JOIN users u ON ma.user_id = u.id
SET ma.reference = u.reference
WHERE ma.user_id IS NOT NULL;

UPDATE meeting_attendance ma
JOIN tbl_campus_data cd ON ma.idno = cd.idno
SET ma.reference = cd.reference
WHERE ma.user_id IS NULL AND ma.idno IS NOT NULL;

-- 2. Fix volunteer_tasks.idno type
ALTER TABLE volunteer_tasks 
MODIFY COLUMN idno VARCHAR(50) DEFAULT NULL;

-- ============================================
-- HIGH PRIORITY FIXES
-- ============================================

-- 3. Add reference to tbl_report_people
ALTER TABLE tbl_report_people 
ADD COLUMN reference INT(11) DEFAULT NULL AFTER idno,
ADD UNIQUE INDEX idx_reference (reference);

UPDATE tbl_report_people rp
JOIN tbl_campus_data cd ON rp.idno = cd.idno
SET rp.reference = cd.reference;

-- 4. Fix new_tbl_people_schedules.reference type
ALTER TABLE new_tbl_people_schedules 
ADD COLUMN reference_int INT(11) DEFAULT NULL AFTER reference;

UPDATE new_tbl_people_schedules 
SET reference_int = CAST(reference AS UNSIGNED)
WHERE reference REGEXP '^[0-9]+$';

-- Verify before dropping old column
-- SELECT COUNT(*) FROM new_tbl_people_schedules WHERE reference_int IS NULL;

-- ============================================
-- MEDIUM PRIORITY - Standardize idno sizes
-- ============================================

ALTER TABLE tbl_campus_data MODIFY COLUMN idno VARCHAR(50) DEFAULT '';
ALTER TABLE meeting_attendance MODIFY COLUMN idno VARCHAR(50) DEFAULT NULL;
ALTER TABLE tbl_report_people MODIFY COLUMN idno VARCHAR(50) NOT NULL;
ALTER TABLE requests MODIFY COLUMN idno VARCHAR(50) DEFAULT NULL;

-- ============================================
-- ADD FOREIGN KEYS (After all fixes above)
-- ============================================

ALTER TABLE users 
ADD CONSTRAINT fk_users_reference 
FOREIGN KEY (reference) REFERENCES tbl_people(id) 
ON DELETE CASCADE;

ALTER TABLE tbl_campus_data 
ADD CONSTRAINT fk_campus_data_reference 
FOREIGN KEY (reference) REFERENCES tbl_people(id) 
ON DELETE CASCADE;

ALTER TABLE tbl_people_attendance 
ADD CONSTRAINT fk_attendance_reference 
FOREIGN KEY (reference) REFERENCES tbl_people(id) 
ON DELETE CASCADE;

ALTER TABLE tbl_people_devotion 
ADD CONSTRAINT fk_devotion_reference 
FOREIGN KEY (reference) REFERENCES tbl_people(id) 
ON DELETE CASCADE;

ALTER TABLE tbl_people_leaves 
ADD CONSTRAINT fk_leaves_reference 
FOREIGN KEY (reference) REFERENCES tbl_people(id) 
ON DELETE CASCADE;

ALTER TABLE tbl_people_schedules 
ADD CONSTRAINT fk_schedules_reference 
FOREIGN KEY (reference) REFERENCES tbl_people(id) 
ON DELETE CASCADE;

ALTER TABLE meeting_attendance 
ADD CONSTRAINT fk_meeting_attendance_reference 
FOREIGN KEY (reference) REFERENCES tbl_people(id) 
ON DELETE SET NULL;

ALTER TABLE tbl_report_people 
ADD CONSTRAINT fk_report_people_reference 
FOREIGN KEY (reference) REFERENCES tbl_people(id) 
ON DELETE CASCADE;
```

---

## Summary Statistics

| Metric | Count |
|--------|-------|
| Total tables analyzed | 80+ |
| Tables with proper structure (both columns) | 9 |
| Tables missing `reference` | 2 (critical) |
| Tables with wrong data types | 3 |
| Foreign keys currently defined | 0 |
| Foreign keys recommended | 8 |

---

## Impact on Data Access Layer

**For your data scoping implementation:**

### If Using `reference` for Scoping:
✅ **Pros:**
- Proper relationships
- Data integrity with foreign keys
- Historical data preserved

❌ **Cons:**
- Requires fixing schema first
- Need to update code (see code audit)
- Migration effort required

### If Using Campus/Ministry/Dept for Scoping:
✅ **Pros:**
- No schema changes needed
- Works with current structure
- Organizational boundaries clear

❌ **Cons:**
- Doesn't fix underlying issues
- Still have reference/idno problems

---

## Recommended Approach

1. **Phase 1:** Fix critical schema issues (meeting_attendance, volunteer_tasks)
2. **Phase 2:** Update code to use `reference` instead of `idno` (see code audit)
3. **Phase 3:** Add foreign key constraints
4. **Phase 4:** Implement data access layer using `reference`

**OR**

1. **Quick Win:** Implement campus-based scoping (no schema changes)
2. **Later:** Fix schema issues gradually
3. **Future:** Migrate to reference-based scoping

---

**End of Database Schema Audit**
