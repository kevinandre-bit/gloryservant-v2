# Database Migration Guide
## Fixing `reference` and `idno` Issues

**Project:** GloryServant v2
**Date:** 2025

---

## ⚠️ IMPORTANT: Read Before Running

**BACKUP YOUR DATABASE FIRST!**

```bash
# Create backup
mysqldump -u root -p u276774975_serveflow_db > backup_$(date +%Y%m%d_%H%M%S).sql

# Or via phpMyAdmin: Export → SQL → Go
```

---

## Migration Files Created

5 migration files have been created in `database/migrations/`:

1. `2025_03_10_000001_add_reference_to_meeting_attendance.php`
2. `2025_03_10_000002_fix_volunteer_tasks_idno_type.php`
3. `2025_03_10_000003_add_reference_to_tbl_report_people.php`
4. `2025_03_10_000004_standardize_idno_column_sizes.php`
5. `2025_03_10_000005_add_foreign_key_constraints.php`

---

## Step-by-Step Execution

### **Phase 1: Verify Current State (REQUIRED)**

Before running migrations, check your data:

```bash
cd /Applications/XAMPP/xamppfiles/htdocs/gloryservant_v2

# Check if tables exist
php artisan tinker
>>> Schema::hasTable('meeting_attendance');
>>> Schema::hasTable('tbl_report_people');
>>> Schema::hasTable('volunteer_tasks');
>>> exit

# Check current data integrity
mysql -u root -p u276774975_serveflow_db
```

```sql
-- Check how many meeting_attendance records have user_id
SELECT 
    COUNT(*) as total,
    COUNT(user_id) as with_user_id,
    COUNT(idno) as with_idno
FROM meeting_attendance;

-- Check if idno matches between tables
SELECT COUNT(*) 
FROM meeting_attendance ma
LEFT JOIN tbl_campus_data cd ON ma.idno = cd.idno
WHERE ma.idno IS NOT NULL AND cd.idno IS NULL;
-- If this returns > 0, you have orphaned idno values

-- Check volunteer_tasks idno values
SELECT idno, COUNT(*) 
FROM volunteer_tasks 
GROUP BY idno;

-- Exit
exit;
```

---

### **Phase 2: Run Migrations One by One**

**DO NOT run all at once!** Test each migration individually.

#### **Migration 1: Add reference to meeting_attendance**

```bash
php artisan migrate --path=database/migrations/2025_03_10_000001_add_reference_to_meeting_attendance.php
```

**Verify:**
```sql
-- Check if column was added
DESCRIBE meeting_attendance;

-- Check how many got populated
SELECT 
    COUNT(*) as total,
    COUNT(reference) as with_reference
FROM meeting_attendance;

-- Check for NULL references (these need manual review)
SELECT id, user_id, idno, reference 
FROM meeting_attendance 
WHERE reference IS NULL 
LIMIT 10;
```

**If something goes wrong:**
```bash
php artisan migrate:rollback --step=1
```

---

#### **Migration 2: Fix volunteer_tasks idno type**

```bash
php artisan migrate --path=database/migrations/2025_03_10_000002_fix_volunteer_tasks_idno_type.php
```

**Verify:**
```sql
DESCRIBE volunteer_tasks;
-- idno should now be VARCHAR(50)

SELECT idno FROM volunteer_tasks LIMIT 5;
```

---

#### **Migration 3: Add reference to tbl_report_people**

```bash
php artisan migrate --path=database/migrations/2025_03_10_000003_add_reference_to_tbl_report_people.php
```

**Verify:**
```sql
DESCRIBE tbl_report_people;

SELECT 
    COUNT(*) as total,
    COUNT(reference) as with_reference
FROM tbl_report_people;

-- Check for unmatched idno
SELECT rp.id, rp.idno, rp.reference
FROM tbl_report_people rp
WHERE rp.reference IS NULL;
```

---

#### **Migration 4: Standardize idno sizes**

```bash
php artisan migrate --path=database/migrations/2025_03_10_000004_standardize_idno_column_sizes.php
```

**Verify:**
```sql
-- Check all idno columns are now VARCHAR(50)
SELECT 
    TABLE_NAME, 
    COLUMN_NAME, 
    COLUMN_TYPE 
FROM INFORMATION_SCHEMA.COLUMNS 
WHERE TABLE_SCHEMA = 'u276774975_serveflow_db' 
  AND COLUMN_NAME = 'idno';
```

---

#### **Migration 5: Add Foreign Keys (OPTIONAL - Run Last)**

⚠️ **ONLY run this if:**
- All previous migrations succeeded
- All `reference` columns have valid data
- You've tested your application

```bash
# First, verify no orphaned references
mysql -u root -p u276774975_serveflow_db
```

```sql
-- Check for invalid references in each table
SELECT 'users' as tbl, COUNT(*) as invalid
FROM users u
LEFT JOIN tbl_people p ON u.reference = p.id
WHERE u.reference IS NOT NULL AND p.id IS NULL

UNION ALL

SELECT 'tbl_campus_data', COUNT(*)
FROM tbl_campus_data cd
LEFT JOIN tbl_people p ON cd.reference = p.id
WHERE cd.reference IS NOT NULL AND p.id IS NULL

UNION ALL

SELECT 'meeting_attendance', COUNT(*)
FROM meeting_attendance ma
LEFT JOIN tbl_people p ON ma.reference = p.id
WHERE ma.reference IS NOT NULL AND p.id IS NULL;

-- If ANY of these return > 0, DO NOT add foreign keys yet!
```

**If all checks pass:**
```bash
php artisan migrate --path=database/migrations/2025_03_10_000005_add_foreign_key_constraints.php
```

---

## Troubleshooting

### **Error: "Cannot add foreign key constraint"**

**Cause:** Orphaned references exist

**Fix:**
```sql
-- Find orphaned records
SELECT u.id, u.reference 
FROM users u
LEFT JOIN tbl_people p ON u.reference = p.id
WHERE u.reference IS NOT NULL AND p.id IS NULL;

-- Option 1: Set to NULL
UPDATE users SET reference = NULL WHERE reference NOT IN (SELECT id FROM tbl_people);

-- Option 2: Delete orphaned records (CAREFUL!)
-- DELETE FROM users WHERE reference NOT IN (SELECT id FROM tbl_people);
```

---

### **Error: "Duplicate entry for key 'unique'"**

**Cause:** Duplicate references in tbl_report_people

**Fix:**
```sql
-- Find duplicates
SELECT reference, COUNT(*) 
FROM tbl_report_people 
WHERE reference IS NOT NULL
GROUP BY reference 
HAVING COUNT(*) > 1;

-- Keep only the first occurrence
DELETE rp1 FROM tbl_report_people rp1
INNER JOIN tbl_report_people rp2 
WHERE rp1.id > rp2.id 
  AND rp1.reference = rp2.reference;
```

---

### **Migration Failed - How to Rollback**

```bash
# Rollback last migration
php artisan migrate:rollback --step=1

# Rollback all 5 migrations
php artisan migrate:rollback --step=5

# Restore from backup
mysql -u root -p u276774975_serveflow_db < backup_YYYYMMDD_HHMMSS.sql
```

---

## Post-Migration Verification

After all migrations succeed:

```sql
-- 1. Check all tables have reference column
SELECT TABLE_NAME, COLUMN_NAME, COLUMN_TYPE
FROM INFORMATION_SCHEMA.COLUMNS
WHERE TABLE_SCHEMA = 'u276774975_serveflow_db'
  AND COLUMN_NAME = 'reference'
ORDER BY TABLE_NAME;

-- 2. Check foreign keys were created
SELECT 
    TABLE_NAME,
    CONSTRAINT_NAME,
    REFERENCED_TABLE_NAME,
    REFERENCED_COLUMN_NAME
FROM INFORMATION_SCHEMA.KEY_COLUMN_USAGE
WHERE TABLE_SCHEMA = 'u276774975_serveflow_db'
  AND REFERENCED_TABLE_NAME = 'tbl_people';

-- 3. Verify data integrity
SELECT 
    'meeting_attendance' as table_name,
    COUNT(*) as total_records,
    COUNT(reference) as with_reference,
    ROUND(COUNT(reference) / COUNT(*) * 100, 2) as percent_populated
FROM meeting_attendance

UNION ALL

SELECT 
    'tbl_report_people',
    COUNT(*),
    COUNT(reference),
    ROUND(COUNT(reference) / COUNT(*) * 100, 2)
FROM tbl_report_people;
```

---

## Alternative: Manual SQL Execution

If you prefer to run SQL directly instead of Laravel migrations:

```bash
mysql -u root -p u276774975_serveflow_db < database/migrations/manual_migration.sql
```

Create `manual_migration.sql`:
```sql
-- See DATABASE_SCHEMA_AUDIT.md for complete SQL script
```

---

## Recommended Execution Order

### **Conservative Approach (Recommended):**

1. ✅ Backup database
2. ✅ Run Migration 1 (meeting_attendance)
3. ✅ Test application
4. ✅ Run Migration 2 (volunteer_tasks)
5. ✅ Test application
6. ✅ Run Migration 3 (tbl_report_people)
7. ✅ Test application
8. ✅ Run Migration 4 (standardize sizes)
9. ✅ Test application
10. ⏸️ WAIT - Test for 1-2 days
11. ✅ Run Migration 5 (foreign keys) - OPTIONAL

### **Aggressive Approach (Not Recommended):**

```bash
# Run all at once (risky!)
php artisan migrate
```

---

## Testing After Migration

1. **Test Personal Dashboards:**
   - Login as different users
   - Check attendance, leaves, schedules display correctly

2. **Test Admin Reports:**
   - Generate attendance reports
   - Export data
   - Check devotion tracking

3. **Test Meeting Attendance:**
   - Check-in to a meeting
   - View attendance history

4. **Monitor Logs:**
```bash
tail -f storage/logs/laravel.log
```

---

## Next Steps After Migration

Once database is fixed:

1. **Update Code** (see IDNO_AUDIT_REPORT.md)
   - Replace `where('idno', ...)` with `where('reference', ...)`
   - Fix JOIN operations

2. **Implement Data Access Layer**
   - Add campus/ministry/dept scoping
   - Use `reference` for user identification

3. **Add Indexes** (if needed)
```sql
CREATE INDEX idx_reference ON meeting_attendance(reference);
CREATE INDEX idx_reference ON tbl_report_people(reference);
```

---

## Support

If you encounter issues:

1. Check `storage/logs/laravel.log`
2. Review error messages carefully
3. Rollback and restore from backup if needed
4. Test on a development database first

---

**End of Migration Guide**
