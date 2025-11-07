-- Migration: Change user_id columns to store idno instead of id

-- Step 1: Update existing data first (before changing column type)
UPDATE workspaces w 
JOIN users u ON w.user_id = u.id 
SET w.user_id = u.idno;

UPDATE projects p
JOIN users u ON p.user_id = u.id
SET p.user_id = u.idno;

UPDATE workspace_user wu
JOIN users u ON wu.user_id = u.id
SET wu.user_id = u.idno;

-- Step 2: Change column types to VARCHAR
ALTER TABLE workspaces 
MODIFY COLUMN user_id VARCHAR(50) NOT NULL;

ALTER TABLE projects
MODIFY COLUMN user_id VARCHAR(50) NOT NULL;

ALTER TABLE workspace_user
MODIFY COLUMN user_id VARCHAR(50) NOT NULL;
