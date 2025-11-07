# Scope Setup Complete ✅

## What Was Done

### 1. Scope Levels Assigned

| Role | Scope Level | What They See |
|------|-------------|---------------|
| **ADMIN** | all | Everything (full access) |
| **MANAGER** | all | Everything (full access) |
| **OVERSEER** | all | Everything (full access) |
| **CAMPUS POC** | campus | Only their campus data |
| **CAMPUS ADMINISTRATOR** | campus | Only their campus data |
| **MINISTRY LEADER** | ministry | Only their ministry data |
| **MINISTRY OVERSEER** | ministry | Only their ministry data |
| **MINISTRY CORE TEAM** | ministry | Only their ministry data |
| All other roles | all | Everything (default) |

### 2. How It Works

**Example 1: Campus POC**
- User: John (Campus POC at Manila campus)
- Role scope: `campus`
- Sees: Only attendance/leaves/schedules from Manila campus
- Cannot see: Data from other campuses

**Example 2: Ministry Leader**
- User: Sarah (Ministry Leader of Youth Ministry at Manila campus)
- Role scope: `ministry`
- Sees: Only attendance/leaves/schedules from Youth Ministry at Manila campus
- Cannot see: Other ministries or other campuses

**Example 3: Admin**
- User: Admin
- Role scope: `all`
- Sees: Everything from all campuses/ministries/departments

---

## Testing Instructions

### Test 1: Verify Scope Filtering

**Login as Campus POC:**
1. Login with a user who has role_id = 6 (CAMPUS POC)
2. Go to attendance page
3. Should only see attendance from their campus
4. Try to access other campus data → should not appear

**Login as Ministry Leader:**
1. Login with a user who has role_id = 14 (MINISTRY LEADER)
2. Go to leaves page
3. Should only see leaves from their ministry
4. Try to access other ministry data → should not appear

**Login as Admin:**
1. Login with a user who has role_id = 7 (ADMIN)
2. Go to any page
3. Should see all data from all campuses/ministries

### Test 2: Check User's Organizational Data

Run this query to verify a user has proper campus/ministry/department data:

```sql
SELECT u.id, u.name, u.reference, ur.role_name, ur.scope_level,
       cd.campus, cd.ministry, cd.department
FROM users u
JOIN users_roles ur ON u.role_id = ur.id
LEFT JOIN tbl_campus_data cd ON u.reference = cd.reference
WHERE u.id = ?;  -- Replace ? with user ID
```

**Expected results:**
- User must have `reference` value
- User must have campus/ministry/department in tbl_campus_data
- If missing, scope filtering won't work for that user

---

## Troubleshooting

### Problem: User sees no data

**Solution 1: Check user has reference**
```sql
SELECT id, name, reference FROM users WHERE id = ?;
```
If reference is NULL, assign it:
```sql
UPDATE users SET reference = ? WHERE id = ?;
```

**Solution 2: Check user has campus data**
```sql
SELECT * FROM tbl_campus_data WHERE reference = ?;
```
If missing, add campus data for the user.

**Solution 3: Check role scope**
```sql
SELECT ur.role_name, ur.scope_level 
FROM users u 
JOIN users_roles ur ON u.role_id = ur.id 
WHERE u.id = ?;
```

### Problem: User sees too much data

**Check role scope is not 'all':**
```sql
UPDATE users_roles SET scope_level = 'campus' WHERE id = ?;
```

---

## Adding Department-Level Scope

If you need department-level filtering:

```sql
-- Create a DEPARTMENT HEAD role (if not exists)
INSERT INTO users_roles (role_name, scope_level) 
VALUES ('DEPARTMENT HEAD', 'department');

-- Or update existing role
UPDATE users_roles 
SET scope_level = 'department' 
WHERE role_name = 'DEPARTMENT HEAD';
```

---

## Modifying Scope Levels

### Change a role's scope:
```sql
-- Make WELLNESS TEAM campus-scoped
UPDATE users_roles 
SET scope_level = 'campus' 
WHERE role_name = 'WELLNESS TEAM';

-- Make HP-TEAM ministry-scoped
UPDATE users_roles 
SET scope_level = 'ministry' 
WHERE role_name = 'HP-TEAM';
```

### Give specific user full access temporarily:
```sql
-- Change user's role to ADMIN
UPDATE users SET role_id = 7 WHERE id = ?;
```

---

## What's Protected Now

✅ **Attendance queries** - Filtered by scope
✅ **Leaves queries** - Filtered by scope
✅ **Schedules queries** - Filtered by scope

All controllers using `table::attendance()`, `table::leaves()`, or `table::schedules()` are automatically protected.

---

## Summary

**Status:** ✅ Ready for testing

**Roles configured:** 16 roles
- 11 with full access (scope = 'all')
- 2 with campus access (scope = 'campus')
- 3 with ministry access (scope = 'ministry')

**Next:** Test with real users to verify filtering works correctly

---

**Setup completed successfully!**
