# Graph Report - c:\laragon\www\sistem_wajib_lapor  (2026-05-05)

## Corpus Check
- Large corpus: 359 files · ~179,387 words. Semantic extraction will be expensive (many Claude tokens). Consider running on a subfolder, or use --no-semantic to run AST-only.

## Summary
- 584 nodes · 284 edges · 328 communities (283 shown, 45 thin omitted)
- Extraction: 95% EXTRACTED · 5% INFERRED · 0% AMBIGUOUS · INFERRED: 14 edges (avg confidence: 0.8)
- Token cost: 0 input · 0 output

## Community Hubs (Navigation)
- [[_COMMUNITY_Admin Participantcontroller Pa|Admin Participantcontroller Pa]]
- [[_COMMUNITY_App Models Warning Php|App Models Warning Php]]
- [[_COMMUNITY_App Models Participant Php|App Models Participant Php]]
- [[_COMMUNITY_Admin Attendancecontroller Att|Admin Attendancecontroller Att]]
- [[_COMMUNITY_App Models Attendanceattempt P|App Models Attendanceattempt P]]
- [[_COMMUNITY_App Models Attendanceperiod Ph|App Models Attendanceperiod Ph]]
- [[_COMMUNITY_App Models User Php|App Models User Php]]
- [[_COMMUNITY_Admin Locationcontroller Locat|Admin Locationcontroller Locat]]
- [[_COMMUNITY_Admin Violationtypecontroller|Admin Violationtypecontroller ]]
- [[_COMMUNITY_App Models Location Php|App Models Location Php]]
- [[_COMMUNITY_Storage Framework Testing Pest|Storage Framework Testing Pest]]
- [[_COMMUNITY_App Http Requests Auth Loginre|App Http Requests Auth Loginre]]
- [[_COMMUNITY_App Services Periodservice Php|App Services Periodservice Php]]
- [[_COMMUNITY_App Http Controllers Profileco|App Http Controllers Profileco]]
- [[_COMMUNITY_App Http Controllers Auth Auth|App Http Controllers Auth Auth]]
- [[_COMMUNITY_App Http Controllers Peserta A|App Http Controllers Peserta A]]
- [[_COMMUNITY_App Http Controllers Peserta P|App Http Controllers Peserta P]]
- [[_COMMUNITY_Admin Storeparticipantrequest|Admin Storeparticipantrequest ]]
- [[_COMMUNITY_Admin Updateparticipantrequest|Admin Updateparticipantrequest]]
- [[_COMMUNITY_App Mail Warningnotificationma|App Mail Warningnotificationma]]
- [[_COMMUNITY_Admin Reportcontroller Reportc|Admin Reportcontroller Reportc]]
- [[_COMMUNITY_App Http Controllers Auth Regi|App Http Controllers Auth Regi]]
- [[_COMMUNITY_App Http Controllers Peserta D|App Http Controllers Peserta D]]
- [[_COMMUNITY_App Providers Appserviceprovid|App Providers Appserviceprovid]]
- [[_COMMUNITY_Database Factories Userfactory|Database Factories Userfactory]]
- [[_COMMUNITY_Profile Partials Delete User F|Profile Partials Delete User F]]
- [[_COMMUNITY_App Console Commands Checkatte|App Console Commands Checkatte]]
- [[_COMMUNITY_App Console Commands Generaten|App Console Commands Generaten]]
- [[_COMMUNITY_Admin Dashboardcontroller Dash|Admin Dashboardcontroller Dash]]
- [[_COMMUNITY_App Http Controllers Auth Emai|App Http Controllers Auth Emai]]
- [[_COMMUNITY_App Http Controllers Auth Emai|App Http Controllers Auth Emai]]
- [[_COMMUNITY_App Http Controllers Auth Veri|App Http Controllers Auth Veri]]
- [[_COMMUNITY_App Http Controllers Peserta H|App Http Controllers Peserta H]]
- [[_COMMUNITY_App Http Middleware Ensureadmi|App Http Middleware Ensureadmi]]
- [[_COMMUNITY_App Http Middleware Ensurepese|App Http Middleware Ensurepese]]
- [[_COMMUNITY_App Http Requests Profileupdat|App Http Requests Profileupdat]]
- [[_COMMUNITY_App Models Violationtype Php|App Models Violationtype Php]]
- [[_COMMUNITY_App View Components Applayout|App View Components Applayout ]]
- [[_COMMUNITY_App View Components Guestlayou|App View Components Guestlayou]]
- [[_COMMUNITY_Database Seeders Adminuserseed|Database Seeders Adminuserseed]]
- [[_COMMUNITY_Database Seeders Attendanceper|Database Seeders Attendanceper]]
- [[_COMMUNITY_Database Seeders Databaseseede|Database Seeders Databaseseede]]
- [[_COMMUNITY_Database Seeders Locationseede|Database Seeders Locationseede]]
- [[_COMMUNITY_Database Seeders Participantus|Database Seeders Participantus]]
- [[_COMMUNITY_Database Seeders Violationtype|Database Seeders Violationtype]]
- [[_COMMUNITY_Admin Locations Form|Admin Locations Form]]
- [[_COMMUNITY_Admin Participants Form|Admin Participants Form]]
- [[_COMMUNITY_App Http Controllers Controlle|App Http Controllers Controlle]]
- [[_COMMUNITY_Layouts Navigation|Layouts Navigation]]
- [[_COMMUNITY_Tests Testcase Php|Tests Testcase Php]]

## God Nodes (most connected - your core abstractions)
1. `Participant` - 15 edges
2. `Warning` - 12 edges
3. `AttendancePeriod` - 10 edges
4. `ActivityLog` - 9 edges
5. `ParticipantController` - 8 edges
6. `AttendanceLog` - 8 edges
7. `User` - 8 edges
8. `LocationController` - 7 edges
9. `ViolationTypeController` - 7 edges
10. `Location` - 7 edges

## Surprising Connections (you probably didn't know these)
- None detected - all connections are within the same source files.

## Communities (328 total, 45 thin omitted)

### Community 0 - "Admin Participantcontroller Pa"
Cohesion: 0.11
Nodes (4): ParticipantController, LogActivityMiddleware, ActivityLog, ActivityLogSeeder

### Community 1 - "App Models Warning Php"
Cohesion: 0.14
Nodes (3): Warning, WarningSeeder, WarningService

### Community 3 - "Admin Attendancecontroller Att"
Cohesion: 0.15
Nodes (3): AttendanceController, AttendanceLog, AttendanceLogSeeder

### Community 10 - "Storage Framework Testing Pest"
Cohesion: 0.25
Nodes (3): Expectation, OppositeExpectation, TestCase

### Community 25 - "Profile Partials Delete User F"
Cohesion: 0.5
Nodes (3): profile.partials.delete-user-form, profile.partials.update-password-form, profile.partials.update-profile-information-form

## Knowledge Gaps
- **9 isolated node(s):** `Controller`, `layouts.navigation`, `profile.partials.update-profile-information-form`, `profile.partials.update-password-form`, `profile.partials.delete-user-form` (+4 more)
  These have ≤1 connection - possible missing edges or undocumented components.
- **45 thin communities (<3 nodes) omitted from report** — run `graphify query` to explore isolated nodes.

## Suggested Questions
_Questions this graph is uniquely positioned to answer:_

- **Why does `AttendanceLog` connect `Admin Attendancecontroller Att` to `App Models Attendanceattempt P`?**
  _High betweenness centrality (0.002) - this node is a cross-community bridge._
- **Are the 4 inferred relationships involving `Warning` (e.g. with `.checkLevel1()` and `.checkLevel2()`) actually correct?**
  _`Warning` has 4 INFERRED edges - model-reasoned connections that need verification._
- **Are the 5 inferred relationships involving `ActivityLog` (e.g. with `.store()` and `.update()`) actually correct?**
  _`ActivityLog` has 5 INFERRED edges - model-reasoned connections that need verification._
- **What connects `Controller`, `layouts.navigation`, `profile.partials.update-profile-information-form` to the rest of the system?**
  _9 weakly-connected nodes found - possible documentation gaps or missing edges._
- **Should `Admin Participantcontroller Pa` be split into smaller, more focused modules?**
  _Cohesion score 0.11 - nodes in this community are weakly interconnected._
- **Should `App Models Warning Php` be split into smaller, more focused modules?**
  _Cohesion score 0.14 - nodes in this community are weakly interconnected._
- **Should `App Models Participant Php` be split into smaller, more focused modules?**
  _Cohesion score 0.14 - nodes in this community are weakly interconnected._