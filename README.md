# SHINE YOUNG — Digital Culture Learning Platform

> *Preserving Rwandan culture. Empowering the next generation.*

**Live URL:** http://shineyoung.rf.gd  
**Author:** Shema  
**Organization:** African Leadership University (ALU)  

---

## 📖 About the Project

Shine Young is a child-focused web-based cultural learning platform that provides young learners aged 5–12 with access to traditional Rwandan stories, proverbs, music, dance videos, and school cultural content. It includes a full admin dashboard for content management and real-time message handling.

**The problem it solves:** Young children in urban Rwanda are losing touch with their cultural heritage due to globalization and the dominance of foreign digital content. Shine Young bridges this gap through safe, supervised, and culturally accurate digital content.

---

##  Features

| Feature | Description |
|---------|-------------|
| Login / Sign Up | Session-based PHP authentication |
| Homepage | Hero, stats, explore cards, contact form |
| Stories | Admin-managed stories with Bloom Library links |
| Proverbs | Kinyarwanda + English + cultural explanations |
| Media | Embedded YouTube cultural videos |
| School Culture | Student clubs and performance videos |
| Contact Form | Saves messages to DB + email notification |
| Admin Dashboard | Full content management + inbox + reply system |

---

##  Tech Stack

| Layer | Technology |
|-------|-----------|
| Frontend | HTML5, CSS3, JavaScript |
| Backend | PHP  |
| Database | MySQL |
| Server | Apache |
| Hosting | InfinityFree (live) / XAMPP (local) |
| Auth | PHP Sessions + MD5 hashing |

---

##  Setup Instructions (Local — XAMPP)

## First we have to change to localhost 
# DO THIS
Here is everything simplified — same change grouped together:

---

## Change 1 — Database Credentials
**Do this in ALL 7 files at once using Ctrl+H (Find & Replace) in Notepad**

Find:
```
sql306.infinityfree.com;dbname=if0_41502487_shema_emmy", "if0_41502487", "Shema2003"
```
Replace with:
```
localhost;dbname=shine_young", "root", ""
```

**The 7 files to do this in:**
| File | Location |
|------|----------|
| `login.php` | `htdocs/` |
| `sign.php` | `htdocs/` |
| `contact_handler.php` | `htdocs/` |
| `dashboard.php` | `htdocs/admin/` |
| `stories.php` | `htdocs/public/` |
| `proverbs.php` | `htdocs/public/` |
| `media.php` | `htdocs/public/` |

---

## Change 2 — Logout Links
**Do this in ALL 5 files at once using Ctrl+H (Find & Replace)**

Find:
```
http://shineyoung.rf.gd
```
Replace with:
```
http://localhost/shine_young
```

**The 5 files to do this in:**
| File | Location |
|------|----------|
| `index.html` | `htdocs/public/` |
| `stories.php` | `htdocs/public/` |
| `proverbs.php` | `htdocs/public/` |
| `media.php` | `htdocs/public/` |
| `school-culture.html` | `htdocs/public/` |

---

That is it — just **2 find & replace operations** across all files and your app runs on localhost. 

### Prerequisites
- [XAMPP](https://www.apachefriends.org) installed on your computer
- A web browser (Chrome, Firefox, or Edge)

---

### Step 1 — Download the project

Click the green **Code** button on this GitHub page → **Download ZIP**  
Extract the ZIP file. You should have a folder called `shine_young`.

---

### Step 2 — Place in XAMPP htdocs

Copy the entire `shine_young` folder into your XAMPP `htdocs` directory:

| Operating System | Path |
|-----------------|------|
| Windows | `C:\xampp\htdocs\shine_young` |
| macOS | `/Applications/XAMPP/htdocs/shine_young` |
| Linux | `/opt/lampp/htdocs/shine_young` |

---

### Step 3 — Start XAMPP services

Open the **XAMPP Control Panel** and press **Start** on:
-  **Apache**
-  **MySQL**

Both should turn green before continuing.

---

### Step 4 — Create the database

1. Open your browser and go to: `http://localhost/phpmyadmin`
2. Click **New** in the left sidebar
3. Type the database name exactly: `shine_young`
4. Click **Create**

---

### Step 5 — Import the SQL file

1. Click on `shine_young` in the left sidebar
2. Click the **Import** tab at the top
3. Click **Choose File** → select `shine_young/shine_young.sql`
4. Scroll down and click **Go**

 You should see: *"Import has been successfully finished. 28 queries executed."*

---

### Step 6 — Open the application

Go to: `http://localhost/shine_young`

Log in with:
| Username | Password |
|----------|----------|
| `admin` | `12345` |
| `rahmy` | `1234567` |

---

### Step 7 — Access the admin dashboard

After logging in, go to:  
`http://localhost/shine_young/admin/dashboard.php`

---

##  Live Deployment (InfinityFree)

The application is publicly deployed at:  
**http://shineyoung.rf.gd**

### To redeploy or update on InfinityFree:

1. Update database credentials in these 4 files:
   - `login.php`
   - `sign.php`
   - `contact_handler.php`
   - `admin/dashboard.php`

   Replace:
   ```php
   $db = new PDO("mysql:host=localhost;dbname=shine_young", "root", "");
   ```
   With your InfinityFree credentials:
   ```php
   $db = new PDO("mysql:host=YOUR_HOST;dbname=YOUR_DBNAME", "YOUR_USER", "YOUR_PASS");
   ```

2. Upload all files to `htdocs/` via InfinityFree File Manager or FileZilla FTP

3. Import `shine_young.sql` via InfinityFree phpMyAdmin

4. Visit your live URL to confirm it works

---

##  Project Structure

```
shine_young/
│
├── index.php               ← Login page
├── signup.php              ← Registration page
├── sign.php                ← Registration handler
├── login.php               ← Login handler
├── contact_handler.php     ← Contact form handler
├── shine_young.sql         ← Full database schema + sample data
├── README.md               ← This file
│
├── public/                 ← Pages shown after login
│   ├── index.html          ← Homepage
│   ├── stories.php         ← Stories (reads from DB)
│   ├── proverbs.php        ← Proverbs (reads from DB)
│   ├── media.php           ← Media videos (reads from DB)
│   ├── school-culture.html ← School Culture page
│   └── style.css           ← Shared stylesheet
│
├── admin/
│   └── dashboard.php       ← Full admin dashboard
│
└── uploads/
    └── stories/            ← Story cover image uploads
```

---

##  Test Accounts

| Username | Password | Role |
|----------|----------|------|
| `admin` | `12345` | Administrator |
| `rahmy` | `1234567` | Regular user |
| `philip` | (see DB) | Regular user |

> Passwords are stored as **MD5 hashes**. Enter plain-text passwords — not the hash.

---

## 📊 Admin Dashboard Features

Access at: `/admin/dashboard.php` (must be logged in)

- **Messages Inbox** — view, filter, search, mark read/unread, reply, delete
- **Stories Manager** — add, edit, delete stories with image upload
- **Proverbs Manager** — add, edit, delete proverbs
- **Media Manager** — add, edit, delete YouTube videos
- **Live Updates** — auto-refreshes every 30 seconds for new messages
- **Built-in Reply** — reply to messages directly from the dashboard

---

##  Troubleshooting

| Problem | Fix |
|---------|-----|
| Blank white page | Check DB credentials in PHP files |
| "Table doesn't exist" | Re-import `shine_young.sql` |
| Login fails | Verify SQL import succeeded; check `users` table exists |
| Content not showing on public pages | Add content via admin dashboard |
| HTTP 500 error | Check PHP file for syntax errors or wrong credentials |


---

