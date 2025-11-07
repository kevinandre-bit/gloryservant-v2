-- Rename tables to add focus_ prefix

RENAME TABLE workspaces TO focus_workspaces;
RENAME TABLE projects TO focus_projects;
RENAME TABLE tasks TO focus_tasks;
RENAME TABLE workspace_user TO focus_workspace_user;
