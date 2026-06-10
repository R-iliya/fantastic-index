<?php
// -------- CONFIG ----------
$ignore = ['vendor', '.git', 'node_modules'];
$path = __DIR__;
header('Content-Type: text/html; charset=utf-8');

// --- AJAX HANDLERS ---
if(isset($_POST['action'])){
    $action = $_POST['action'];
    if($action==='create'){
        $folder = trim($_POST['name']);
        if($folder && !file_exists($folder)){
            mkdir($folder, 0777, true);
            echo json_encode(['success'=>true]);
        } else echo json_encode(['success'=>false,'error'=>'Folder exists or invalid']);
        exit;
    }
    if($action==='rename'){
        $old = $_POST['old']; $new = $_POST['new'];
        if(file_exists($old) && !file_exists($new)){
            rename($old,$new);
            echo json_encode(['success'=>true]);
        } else echo json_encode(['success'=>false,'error'=>'Invalid rename']);
        exit;
    }
    if($action==='delete'){
        $target = $_POST['target'];
        if(is_dir($target)){
            $files = new RecursiveIteratorIterator(new RecursiveDirectoryIterator($target, FilesystemIterator::SKIP_DOTS), RecursiveIteratorIterator::CHILD_FIRST);
            foreach($files as $file){ $file->isDir() ? rmdir($file) : unlink($file); }
            rmdir($target);
            echo json_encode(['success'=>true]);
        } elseif(is_file($target)){
            unlink($target);
            echo json_encode(['success'=>true]);
        } else echo json_encode(['success'=>false,'error'=>'Not found']);
        exit;
    }
}

// --- GET DATA ---
$dirs = array_filter(glob('*'), fn($d)=>is_dir($d) && !in_array($d,$ignore));
$files = array_filter(glob('*'), fn($f)=>is_file($f));

$data = [];
foreach($dirs as $dir){
    $filesInside = glob("$dir/*");
    $fileCount = $filesInside ? count($filesInside) : 0;

    $data[] = ['name'=>$dir,'mtime'=>filemtime($dir),'files'=>$fileCount];
}

$fileData = [];
foreach($files as $file){
    $fileData[] = ['name'=>$file,'mtime'=>filemtime($file),'size'=>filesize($file)];
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<title>Index</title>
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<style>
:root {
    --bg: #0e0e10;
    --card: #1b1b1f;
    --text: #e5e5e7;
    --text-muted: #a1a1aa;
    --accent: #6366f1;
    --radius: 16px;
    --shadow: 0 10px 15px -3px rgb(0 0 0 / 0.1), 0 4px 6px -4px rgb(0 0 0 / 0.1);
}

body.light {
    --bg: #f8f9fc;
    --card: #ffffff;
    --text: #18181b;
    --text-muted: #71717a;
}

* { box-sizing: border-box; }

body {
    background: var(--bg);
    color: var(--text);
    font-family: Inter, system-ui, -apple-system, sans-serif;
    margin: 0;
    padding: 48px 24px;
    line-height: 1.5;
    transition: background 0.3s;
}

h1 {
    font-size: 2.75rem;
    font-weight: 700;
    text-align: center;
    margin-bottom: 2rem;
    background: linear-gradient(90deg, var(--accent), #a5b4fc);
    -webkit-background-clip: text;
    -webkit-text-fill-color: transparent;
}

.controls {
    display: flex;
    justify-content: center;
    gap: 12px;
    margin-bottom: 2.5rem;
    flex-wrap: wrap;
    max-width: 1200px;
    margin-left: auto;
    margin-right: auto;
}

input, select, button {
    background: var(--card);
    color: var(--text);
    border: 1px solid rgba(99, 102, 241, 0.3);
    padding: 12px 16px;
    border-radius: var(--radius);
    font-size: 1rem;
    transition: all 0.2s;
}

input:focus, select:focus {
    outline: none;
    border-color: var(--accent);
    box-shadow: 0 0 0 3px rgba(99, 102, 241, 0.15);
}

input { width: 240px; }

button { cursor: pointer; font-weight: 500; }

button:hover {
    background: var(--accent);
    color: white;
    border-color: var(--accent);
}

.section-title {
    max-width: 1200px;
    margin: 3rem auto 1rem;
    font-size: 1.35rem;
    font-weight: 600;
    opacity: 0.75;
    padding-left: 8px;
}

/* Main Grid */
.grid {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(280px, 1fr));
    grid-auto-flow: row dense;
    gap: 24px;
    max-width: 1200px;
    margin: 0 auto;
}

/* Card */
.item {
    background: var(--card);
    border-radius: var(--radius);
    padding: 20px;
    display: flex;
    flex-direction: column;
    border: 2px solid transparent;
    transition: all 0.25s cubic-bezier(0.4, 0, 0.2, 1);
    box-shadow: var(--shadow);
    position: relative;
    overflow: hidden;
    height: 100%;
}

.item:hover {
    transform: translateY(-6px);
    border-color: var(--accent);
    box-shadow: 0 20px 25px -5px rgb(0 0 0 / 0.15), 0 8px 10px -6px rgb(0 0 0 / 0.15);
}

/* Content */
.item-content {
    display: flex;
    align-items: flex-start;
    gap: 16px;
    flex: 1;
    min-width: 0;
}

.left {
    display: flex;
    gap: 16px;
    align-items: flex-start;
    flex: 1;
    min-width: 0;
}

.icon svg {
    width: 32px;
    height: 32px;
    flex-shrink: 0;
    margin-top: 2px;
    stroke: var(--accent);
}

.info {
    flex: 1;
    min-width: 0;
    overflow: hidden;
}

.name {
    font-size: 1.15rem;
    font-weight: 600;
    margin-bottom: 4px;
    word-break: break-word;
    overflow: hidden;
    display: -webkit-box;
    -webkit-line-clamp: 2;
    -webkit-box-orient: vertical;
}

.files {
    font-size: 0.9rem;
    color: var(--text-muted);
}

/* Actions */
.actions {
    display: flex;
    gap: 8px;
    margin-top: 12px;
    flex-wrap: wrap;
}

.action-btn {
    padding: 6px 12px;
    font-size: 0.85rem;
    border-radius: 8px;
    background: rgba(99, 102, 241, 0.1);
    color: var(--accent);
    border: none;
    cursor: pointer;
    transition: all 0.2s;
    white-space: nowrap;
}

.action-btn:hover {
    background: var(--accent);
    color: white;
}

/* Responsive */
@media (max-width: 640px) {
    body { padding: 24px 16px; }
    .grid { 
        grid-template-columns: repeat(auto-fit, minmax(240px, 1fr)); 
        gap: 20px; 
    }
    h1 { font-size: 2.25rem; }
}
</style>
</head>
<body>
<h1>Index</h1>

<div class="controls">
    <input type="text" id="search" placeholder="Search Files...">
    <select id="sort">
        <option value="az">A → Z</option>
        <option value="za">Z → A</option>
        <option value="new">Newest first</option>
        <option value="old">Oldest first</option>
    </select>
    <button id="mode">Light / Dark</button>
    <button onclick="createFolder()">+ New Folder</button>
</div>

<div class="section-title">Folders</div>
<div class="grid" id="grid">
<?php foreach($data as $dir): ?>
    <div class="item" data-name="<?= strtolower($dir['name']) ?>" data-date="<?= $dir['mtime'] ?>">
        <a href="<?= htmlspecialchars($dir['name']) ?>" style="text-decoration:none; color:inherit; flex:1; display:flex; flex-direction:column;">
            <div class="item-content">
                <div class="left">
                    <div class="icon">
                        <svg fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 7l2-2h6l2 2h8v12H3z"/>
                        </svg>
                    </div>
                    <div class="info">
                        <div class="name"><?= htmlspecialchars($dir['name']) ?></div>
                        <div class="files"><?= $dir['files'] ?> files</div>
                    </div>
                </div>
            </div>
        </a>
        <div class="actions">
            <button class="action-btn" onclick="renameItem('<?= htmlspecialchars($dir['name']) ?>')">Rename</button>
            <button class="action-btn" onclick="deleteItem('<?= htmlspecialchars($dir['name']) ?>')">Delete</button>
        </div>
    </div>
<?php endforeach; ?>
</div>

<div class="section-title">Files</div>
<div class="grid" id="filegrid">
<?php foreach($fileData as $file): ?>
    <div class="item" data-name="<?= strtolower($file['name']) ?>" data-date="<?= $file['mtime'] ?>">
        <div class="item-content">
            <div class="left">
                <div class="icon">
                    <svg fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"/>
                    </svg>
                </div>
                <div class="info">
                    <div class="name"><?= htmlspecialchars($file['name']) ?></div>
                    <div class="files"><?= round($file['size']/1024, 1) ?> KB</div>
                </div>
            </div>
        </div>
        <div class="actions">
            <button class="action-btn" onclick="renameItem('<?= htmlspecialchars($file['name']) ?>')">Rename</button>
            <button class="action-btn" onclick="deleteItem('<?= htmlspecialchars($file['name']) ?>')">Delete</button>
        </div>
    </div>
<?php endforeach; ?>
</div>

<script>
// Theme
const modeBtn = document.getElementById('mode');
if(localStorage.theme === 'light') document.body.classList.add('light');

modeBtn.onclick = () => {
    document.body.classList.toggle('light');
    localStorage.theme = document.body.classList.contains('light') ? 'light' : 'dark';
};

// Search
const search = document.getElementById('search');
const allItems = [...document.querySelectorAll('.item')];

search.addEventListener('input', () => {
    const q = search.value.toLowerCase();
    allItems.forEach(item => {
        const name = item.dataset.name || '';
        item.style.display = name.includes(q) ? '' : 'none';
    });
});

// Sort
document.getElementById('sort').addEventListener('change', () => {
    const type = document.getElementById('sort').value;
    const sections = [document.getElementById('grid'), document.getElementById('filegrid')];
    
    sections.forEach(section => {
        let arr = [...section.children];
        if(type === 'az') arr.sort((a,b) => a.textContent.localeCompare(b.textContent));
        if(type === 'za') arr.sort((a,b) => b.textContent.localeCompare(a.textContent));
        if(type === 'new') arr.sort((a,b) => parseInt(b.dataset.date) - parseInt(a.dataset.date));
        if(type === 'old') arr.sort((a,b) => parseInt(a.dataset.date) - parseInt(b.dataset.date));
        
        section.innerHTML = '';
        arr.forEach(el => section.appendChild(el));
    });
});

function createFolder(){
    let name = prompt('Folder name:');
    if(!name) return;
    fetch('',{method:'POST',headers:{'Content-Type':'application/x-www-form-urlencoded'},body:'action=create&name='+encodeURIComponent(name)})
    .then(r=>r.json()).then(r => r.success ? location.reload() : alert(r.error));
}

function renameItem(oldName){
    let newName = prompt('New name:', oldName);
    if(!newName || newName === oldName) return;
    fetch('',{method:'POST',headers:{'Content-Type':'application/x-www-form-urlencoded'},body:`action=rename&old=${encodeURIComponent(oldName)}&new=${encodeURIComponent(newName)}`})
    .then(r=>r.json()).then(r => r.success ? location.reload() : alert(r.error));
}

function deleteItem(target){
    if(!confirm(`Delete ${target}?`)) return;
    fetch('',{method:'POST',headers:{'Content-Type':'application/x-www-form-urlencoded'},body:'action=delete&target='+encodeURIComponent(target)})
    .then(r=>r.json()).then(r => r.success ? location.reload() : alert(r.error));
}
</script>
</body>
</html>