# CLAUDE.md

This file provides guidance to Claude Code (claude.ai/code) when working with code in this repository.

## Running the Project

This is a PHP/MySQL application. Serve it with XAMPP (or any Apache+PHP stack):
- Place the project folder under `htdocs/`
- Start Apache and MySQL in XAMPP Control Panel
- Access via `http://localhost/OCR-and-Recommendation-Hotel-System/`

Environment variables live in `.env` (gitignored). Copy `.env.example` if present, or set:
```
DB_HOST=localhost
DB_USERNAME=root
DB_PASSWORD=
DB_NAME=hotel-check-in/out
GOOGLE_API_KEY=your_key_here
```

## Architecture

### Authentication Flow
The system uses **face recognition** (FaceAPI.js) instead of passwords. From `index.php`:
1. User picks a role (Admin or Reception) and triggers face scan via webcam
2. JavaScript matches the face against stored images in `/label/<name>/` directories
3. On match, AJAX calls `getUserdetails.php` which queries the `guests` table for role
4. **`getUserdetails.php` sets `$_SESSION['logged_in']` and `$_SESSION['role']`** — this is the auth gate
5. JS then redirects to the appropriate page

Check-In and Check-Out pages are intentionally accessible without login (the "Check-In" button on the home page is a direct link).

### Session Rule
**Every page that includes `includes/sidebar.php` must call `session_start()` as its very first PHP statement** — before any output or includes. The sidebar reads `$_SESSION['logged_in']` to conditionally show nav items; if no session is active, it falls back to showing only Home, Check-Out, and Check-In.

### Page → Role Access Pattern
| Pages | Requires Login |
|-------|---------------|
| `CheckIn.php`, `CheckOut.php` | No |
| `RegesterNewGuest.php`, `Edit.php`, `DailyReport.php`, `RegesterNew.php`, `RecommendationService.php`, `RecommendationSettings.php` | Yes (`$_SESSION['logged_in']`) |

### Key Files
- `includes/sidebar.php` — shared sidebar; uses `$_SESSION['logged_in']` to hide restricted nav items
- `includes/head.php` — shared CSS/font imports
- `connect.php` — MySQLi connection via `env()` helper from `env-loader.php`
- `getUserdetails.php` — AJAX endpoint called after face match; sets session and returns JSON `{name, role}`
- `CheckInTable.php` — partial included inside `CheckIn.php` and fetched via AJAX for live refresh
- `process_ocr.php` — handles passport OCR (MRZ scanning) for guest registration
- `face-recognition-javascript-webcam-faceapi-main/FaceRec.php` — standalone face-recognition page used after login for some flows
- `FaceRecForRoles.js` — JavaScript for the three role-based face recognition flows on `index.php`

### Database
Single database `hotel-check-in/out`. Main tables used across the codebase:
- `guests` — stores guest and staff records with a `role` column (`admin`, `receptionist`, etc.)
- `check_logs` — check-in/out log with `guest_id`, `check_in_time`, `return_time` (NULL = currently checked out)

### AJAX Patterns
Several pages use jQuery AJAX to refresh partials without full page reloads:
- `CheckIn.php` fetches `CheckInTable.php` via POST to refresh the pending list
- `checkin_action.php` / `checkout_action.php` are plain POST endpoints that return plain-text responses
- `getUserdetails.php`, `get_rooms.php`, `search_guests.php` return JSON
