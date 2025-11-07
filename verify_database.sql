-- ============================================
-- Database Verification Script
-- Run this BEFORE migrations to check current state
-- ============================================

USE u276774975_serveflow_db;

-- 1. Check which tables have reference column
SELECT '=== Tables with reference column ===' as info;
SELECT TABLE_NAME, COLUMN_NAME, COLUMN_TYPE, IS_NULLABLE
FROM INFORMATION_SCHEMA.COLUMNS
WHERE TABLE_SCHEMA = 'u276774975_serveflow_db'
  AND COLUMN_NAME = 'reference'
ORDER BY TABLE_NAME;

-- 2. Check which tables have idno column
SELECT '=== Tables with idno column ===' as info;
SELECT TABLE_NAME, COLUMN_NAME, COLUMN_TYPE, IS_NULLABLE
FROM INFORMATION_SCHEMA.COLUMNS
WHERE TABLE_SCHEMA = 'u276774975_serveflow_db'
  AND COLUMN_NAME = 'idno'
ORDER BY TABLE_NAME;

-- 3. Check meeting_attendance current state
SELECT '=== meeting_attendance analysis ===' as info;
SELECT 
    COUNT(*) as total_records,
    COUNT(user_id) as has_user_id,
    COUNT(idno) as has_idno,
    COUNT(CASE WHEN user_id IS NULL AND idno IS NULL THEN 1 END) as no_identifier
FROM meeting_attendance;

-- 4. Check for orphaned idno in meeting_attendance
SELECT '=== Orphaned idno in meeting_attendance ===' as info;
SELECT COUNT(*) as orphaned_count
FROM meeting_attendance ma
LEFT JOIN tbl_campus_data cd ON ma.idno = cd.idno
WHERE ma.idno IS NOT NULL AND cd.idno IS NULL;

-- 5. Check volunteer_tasks idno type
SELECT '=== volunteer_tasks idno type ===' as info;
SELECT COLUMN_TYPE 
FROM INFORMATION_SCHEMA.COLUMNS
WHERE TABLE_SCHEMA = 'u276774975_serveflow_db'
  AND TABLE_NAME = 'volunteer_tasks'
  AND COLUMN_NAME = 'idno';

-- 6. Check tbl_report_people
SELECT '=== tbl_report_people analysis ===' as info;
SELECT 
    COUNT(*) as total_records,
    COUNT(DISTINCT idno) as unique_idno
FROM tbl_report_people;

-- 7. Check for potential foreign key violations
SELECT '=== Potential FK violations ===' as info;

SELECT 'users' as table_name, COUNT(*) as invalid_references
FROM users u
LEFT JOIN tbl_people p ON u.reference = p.id
WHERE u.reference IS NOT NULL AND p.id IS NULL

UNION ALL

SELECT 'tbl_campus_data', COUNT(*)
FROM tbl_campus_data cd
LEFT JOIN tbl_people p ON cd.reference = p.id
WHERE cd.reference IS NOT NULL AND p.id IS NULL

UNION ALL

SELECT 'tbl_people_attendance', COUNT(*)
FROM tbl_people_attendance pa
LEFT JOIN tbl_people p ON pa.reference = p.id
WHERE pa.reference IS NOT NULL AND p.id IS NULL;

-- 8. Check idno column sizes
SELECT '=== idno column sizes ===' as info;
SELECT 
    TABLE_NAME,
    COLUMN_TYPE,
    CHARACTER_MAXIMUM_LENGTH
FROM INFORMATION_SCHEMA.COLUMNS
WHERE TABLE_SCHEMA = 'u276774975_serveflow_db'
  AND COLUMN_NAME = 'idno'
ORDER BY TABLE_NAME;

-- 9. Summary
SELECT '=== SUMMARY ===' as info;
SELECT 
    'Total tables' as metric,
    COUNT(DISTINCT TABLE_NAME) as value
FROM INFORMATION_SCHEMA.COLUMNS
WHERE TABLE_SCHEMA = 'u276774975_serveflow_db'

UNION ALL

SELECT 
    'Tables with reference',
    COUNT(DISTINCT TABLE_NAME)
FROM INFORMATION_SCHEMA.COLUMNS
WHERE TABLE_SCHEMA = 'u276774975_serveflow_db'
  AND COLUMN_NAME = 'reference'

UNION ALL

SELECT 
    'Tables with idno',
    COUNT(DISTINCT TABLE_NAME)
FROM INFORMATION_SCHEMA.COLUMNS
WHERE TABLE_SCHEMA = 'u276774975_serveflow_db'
  AND COLUMN_NAME = 'idno';
