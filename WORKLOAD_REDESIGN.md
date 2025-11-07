# Workload View Redesign - Weekly Focus Planning

## Overview
Redesigned the workload view to focus on weekly planning with drag-and-drop task assignment to specific weekdays.

## Key Features

### 1. Compact Priority Summary (Top)
- Single gradient card showing all priority counts (A/B/C/D) in one glance
- Clean, scannable overview of workload distribution

### 2. Needs Immediate Attention
- Red-bordered card highlighting critical tasks (A/B priority + overdue/this week)
- Shows top 5 most urgent tasks with drag handles
- Displays overdue days or days remaining

### 3. Weekly Focus Planning (Left Column - 8/12 width)
- **7-day calendar view** (today + next 6 days)
- Each day is a **drop zone** for tasks
- Drag tasks from "All Tasks" pool or "Immediate Attention" section
- Today is highlighted with blue background
- Tasks show: drag handle, checkbox, priority badge, title, project name, remove button
- Tasks persist in database via `focus_task_sessions` table

### 4. All Tasks Pool
- Scrollable list of all incomplete tasks
- Each task is draggable to any day
- Shows priority badge, checkbox, title, and project name

### 5. Right Sidebar (4/12 width)
- **Due This Week**: Yellow card with tasks due in next 7 days
- **Due Next Week**: Blue card with tasks due in 8-14 days  
- **Projects Summary**: Compact list showing:
  - Priority badge and color-coded left border
  - Project name and deadline
  - Progress bar
  - Task completion ratio (incomplete/total)

## User Workflow

1. **View Overview**: See priority distribution at top
2. **Address Critical**: Review "Needs Immediate Attention" section
3. **Plan Week**: Drag tasks to specific days to create your weekly focus
4. **Monitor Deadlines**: Check right sidebar for upcoming due dates
5. **Track Projects**: See project health in compact cards

## Technical Implementation

### Drag & Drop
- HTML5 drag and drop API
- Visual feedback (opacity, border highlight)
- AJAX calls to persist task-to-day assignments

### Database
- `focus_task_sessions` table stores task assignments to dates
- Unique constraint on (task_id, focus_date)
- Tasks can be assigned to multiple days

### Routes
- `POST /admin/tasks/{task}/focus` - Add task to focus date
- `DELETE /admin/tasks/{task}/focus/{date}` - Remove task from focus date

### Styling
- Hover effects on draggable items
- Color-coded priority badges (A=red, B=orange, C=blue, D=green)
- Responsive layout (left 8 cols, right 4 cols)
- Scrollable sections with max-height

## Benefits

✅ **User Control**: Manually plan your week instead of auto-generated lists
✅ **Visual Planning**: See your entire week at a glance
✅ **Flexible**: Drag tasks to any day, remove anytime
✅ **Context Aware**: Projects moved to sidebar (not primary focus)
✅ **Deadline Focused**: Right sidebar keeps deadlines visible
✅ **Clean UI**: Removed clutter, improved scannability
