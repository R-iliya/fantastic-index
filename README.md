# Fantastic Index 🚀 ![authoor](https://img.shields.io/badge/By:-R._iliya-green)

![GitHub stars](https://img.shields.io/github/stars/R-iliya/fantastic-index?style=flat&color=4A2BE3)
![GitHub forks](https://img.shields.io/github/forks/R-iliya/fantastic-index?style=flat&color=4A2BE3)
![GitHub issues](https://img.shields.io/github/issues/R-iliya/fantastic-index?style=flat&color=4A2BE3)
![GitHub pull requests](https://img.shields.io/github/issues-pr/R-iliya/fantastic-index?style=flat&color=4A2BE3)
![GitHub license](https://img.shields.io/github/license/R-iliya/Axon?style=flat&color=4A2BE3)
![GitHub last commit](https://img.shields.io/github/last-commit/R-iliya/NoxeBrowser?style=flat&color=4A2BE3)



**PHP-Based Modern File Explorer** with sleek design and cloud-drive-like functionality. Browse folders, view files, create new folders, rename or delete items, and more - all in a smooth, interactive interface.

---

## ⚠️ WARNING: USE AT YOUR OWN RISK

This script is **extremely powerful**. It can **create, rename, and delete any file or folder** in the directory it runs in.

I take **zero responsibility** for anything that happens if you run this.
If you accidentally delete your server, your website, or anything else - **that’s on you**.

Seriously. Only run this in a **safe, isolated environment**.

By using this project, you acknowledge that you are fully responsible for all consequences.

---

## Features

* Modern, sleek, responsive design.
* Light/Dark theme toggle.
* View **folders and files** in a clean grid.
* **Folder previews** on hover (images and text snippets).
* **Search** and **sort** folders and files (A→Z, Z→A, newest, oldest).
* **Create new folders** directly from the interface.
* **Rename** and **delete** folders or files with confirmation.
* Smooth **hover animations** and interactive UI.
* Works entirely in PHP (no database required).

---

## Installation

1. **Download or clone the repository**

```bash
git clone https://github.com/R-iliya/fantastic-index.git
```

2. **Move the file to your PHP-enabled server**
   Can be your local XAMPP, MAMP, WAMP, or a live server.

3. **Set permissions** (if necessary)
   Make sure the PHP process can read, write, and delete files in the folder:

```bash
chmod -R 755 /path/to/fantastic-index
```

4. **Open `index.php` in your browser**
   You should see the clean dashboard with all folders and files listed.

---

## How to Use

### Browsing

* Click on any folder to **open it**.
* Click on any file to **download or open it** in the browser (if supported).

### Searching & Sorting

* Use the **search bar** to filter folders or files by name.
* Use the **sort dropdown** to arrange items by name or modification date.

### Folder Management

* **Create Folder:** Click `+ New Folder` → enter folder name → folder appears instantly.
* **Rename:** Click `Rename` next to any folder or file → type new name → updated instantly.
* **Delete:** Click `Delete` next to any folder or file → confirm → item is removed.

> All actions happen via AJAX, so the page doesn’t reload unnecessarily.

### Theme Toggle

* Click the **Light/Dark button** in the top controls to switch themes.
* Your preference is **saved in local storage** for next visit.

---

## Recommended Usage

* Use only on **test servers or safe directories**.
* Do **not run this on production servers** with sensitive data.
* Make **backups** before testing destructive actions like delete or rename.

---

## Planned/Upcoming Enhancements

* Inline folder opening without leaving the page.
* Drag-and-drop **file uploads**.
* Recursive folder browsing.
* User authentication for safer access.

---

## Credits

* Built by MR_iliya
* Inspired by modern cloud storage interfaces and minimal UI aesthetics.

---

## License

This is Unlicense — free for public use without restrictions.
You can do whatever you want with this project. No warranty is provided.
