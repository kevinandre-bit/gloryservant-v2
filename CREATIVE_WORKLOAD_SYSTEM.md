# Creative Workload Management System

## ✅ **Phase 1 Complete: Foundation**

### **Database Tables Created (9 tables)**
1. ✅ `tbl_creative_requests` - Main creative requests
2. ✅ `tbl_creative_tasks` - Task breakdown
3. ✅ `tbl_creative_task_assignments` - Task assignments (pivot)
4. ✅ `tbl_creative_task_events` - Audit trail
5. ✅ `tbl_creative_points_ledger` - Points transactions
6. ✅ `tbl_creative_badges` - Badge definitions
7. ✅ `tbl_creative_user_badges` - Earned badges
8. ✅ `tbl_creative_contribution_snapshots` - Performance cache
9. ✅ `tbl_creative_attachments` - File uploads

### **Models Created (6 models)**
1. ✅ `CreativeRequest` - With relationships
2. ✅ `CreativeTask` - With relationships
3. ✅ `CreativeTaskEvent` - Audit logging
4. ✅ `CreativePointsLedger` - Points tracking
5. ✅ `CreativeBadge` - Badge system
6. ✅ `CreativeAttachment` - File management

### **Services Created (2 services)**
1. ✅ `PointsService` - Award & calculate points with idempotency
2. ✅ `BadgeService` - Check & award badges (Sprinter, Closer, Loyal, Creative Pro)

### **Controllers Created (3 controllers)**
1. ✅ `CreativeRequestController` - CRUD for requests
2. ✅ `CreativeTaskController` - Task management with auto-points
3. ✅ `CreativeAnalyticsController` - Dashboard & leaderboard

### **Routes Added**
- ✅ `/admin/creative` - Dashboard
- ✅ `/admin/creative/requests` - Request list
- ✅ `/admin/creative/requests/create` - New request
- ✅ `/admin/creative/requests/{id}` - View request
- ✅ `/admin/creative/tasks` - Task management

### **Views Created**
- ✅ Analytics Dashboard (`admin/creative/analytics/index.blade.php`)
- ✅ Requests Index (`admin/creative/requests/index.blade.php`)

### **Sidebar Integration**
- ✅ Added "Creative Workload" menu under "CREATIVE TEAM" section
- ✅ Dashboard & Requests links

### **Helper Integration**
- ✅ Added `table::creativeRequests()` helper
- ✅ Added `table::creativeTasks()` helper
- ✅ Integrated with existing scope system (campus/ministry/dept)

---

## 🎯 **System Features**

### **Request Management**
- Create requests (Video/Graphic/Audio/Other)
- Priority levels (Low/Normal/High/Urgent)
- Status tracking (Pending → In Progress → Review → Completed)
- Campus/Ministry/Department scoping

### **Task Management**
- Break requests into tasks
- Assign to multiple volunteers
- Track estimated time
- Auto-award points on completion

### **Points System**
- **Base:** +10 points per completed task
- **Priority Bonus:** +5 (high), +10 (urgent)
- **Time Bonus:** +1 per 30 minutes clocked in (via scheduled job)
- **Idempotency:** Prevents double-awards

### **Badge System**
- **Sprinter:** 5 tasks in 7 days
- **Closer:** 20 tasks in a month
- **Loyal:** 40 hours in a month
- **Creative Pro:** 100 lifetime tasks

### **Analytics**
- Total requests count
- Pending requests
- Active tasks
- Completed today
- Top 10 contributors leaderboard

---

## 📋 **Next Steps (Phase 2)**

### **To Complete:**
1. **Create Request Form** (`requests/create.blade.php`)
2. **Request Detail View** (`requests/show.blade.php`) with task list
3. **Volunteer Dashboard** (Personal view)
4. **Scheduled Jobs:**
   - Daily points calculation from attendance
   - Weekly leaderboard generation
   - Badge award checks
   - Overdue task reminders
5. **Notifications:**
   - Task assigned
   - Task completed
   - Badge earned
6. **Charts:**
   - Workload over time
   - Task distribution by type
   - Contribution trends
7. **Export Reports** (PDF/Excel)

---

## 🔧 **How to Use**

### **Access the System:**
1. Login as admin
2. Navigate to sidebar: **Creative Workload → Dashboard**
3. View analytics and leaderboard
4. Click "Requests" to manage creative requests

### **Create a Request:**
1. Go to **Creative Workload → Requests**
2. Click "New Request"
3. Fill in title, type, priority, due date
4. Submit

### **Manage Tasks:**
1. Open a request
2. Add tasks with assignees
3. Update task status
4. Points auto-awarded on completion

### **View Leaderboard:**
1. Go to **Creative Workload → Dashboard**
2. See top 10 contributors
3. Points calculated from completed tasks + time logged

---

## 🗄️ **Database Schema**

### **Key Relationships:**
- `tbl_creative_requests.requester_people_id` → `tbl_people.id`
- `tbl_creative_tasks.request_id` → `tbl_creative_requests.id`
- `tbl_creative_task_assignments.people_id` → `tbl_people.id`
- `tbl_creative_points_ledger.people_id` → `tbl_people.id`
- Integrates with `tbl_people_attendance` for time-based points

### **Scoping:**
- Filters by `campus_id`, `ministry_id`, `department_id`
- Uses existing `permission::hasFullAccess()` system
- Respects user scope from `tbl_campus_data`

---

## 🚀 **Testing**

### **Test the System:**
```bash
# Access dashboard
http://your-domain/admin/creative

# View requests
http://your-domain/admin/creative/requests

# Create request
http://your-domain/admin/creative/requests/create
```

### **Seed Test Data:**
```php
// Create a test request
CreativeRequest::create([
    'title' => 'Sunday Service Video',
    'description' => 'Edit and produce Sunday service video',
    'request_type' => 'video',
    'priority' => 'high',
    'status' => 'pending',
    'requester_people_id' => 1, // Your people ID
    'desired_due_at' => now()->addDays(7),
]);
```

---

## 📊 **Points Calculation Example**

**Scenario:** Volunteer completes an urgent video task + logs 4 hours

1. **Task Completion:** +10 points (base)
2. **Priority Bonus:** +10 points (urgent)
3. **Time Logged:** +8 points (240 minutes ÷ 30)
4. **Total:** 28 points

---

## 🎖️ **Badge Criteria**

| Badge | Criteria | Check Frequency |
|-------|----------|----------------|
| Sprinter | 5 tasks in 7 days | On task completion |
| Closer | 20 tasks in current month | On task completion |
| Loyal | 40 hours in current month | Daily (via job) |
| Creative Pro | 100 lifetime tasks | On task completion |

---

## ✅ **System Status**

- ✅ Database: Migrated & seeded
- ✅ Models: Created with relationships
- ✅ Controllers: Basic CRUD implemented
- ✅ Routes: Registered
- ✅ Views: Dashboard & list views
- ✅ Sidebar: Integrated
- ✅ Points: Auto-award on completion
- ✅ Badges: Seeded (4 badges)
- ⏳ Scheduled Jobs: Not yet created
- ⏳ Notifications: Not yet created
- ⏳ Charts: Not yet implemented
- ⏳ Exports: Not yet implemented

---

## 🔐 **Security**

- Uses existing auth middleware
- Respects campus/ministry/dept scoping
- Foreign keys enforce data integrity
- Idempotency keys prevent duplicate points
- Uses `people_id` (not `idno`) for relationships

---

**System is ready for testing and further development!** 🎉
