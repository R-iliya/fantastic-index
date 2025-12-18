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

    $preview = null;
    $previewFiles = glob("$dir/*.{png,jpg,jpeg,webp,gif,txt,md,html,php}", GLOB_BRACE);
    if($previewFiles){
        $f = $previewFiles[0];
        $ext = strtolower(pathinfo($f, PATHINFO_EXTENSION));
        if(in_array($ext,['png','jpg','jpeg','gif','webp'])) $preview="<img src='$f' style='width:100%;height:auto;border-radius:12px;'>";
        else $preview="<div style='padding:10px;font-size:13px;opacity:.8;line-height:1.4;'>".substr(strip_tags(file_get_contents($f)),0,150)."...</div>";
    }

    $data[] = ['name'=>$dir,'mtime'=>filemtime($dir),'files'=>$fileCount,'preview'=>$preview];
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
<title>Projects</title>
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<style>
:root{--bg:#0e0e10;--card:#1b1b1f;--text:#e5e5e7;--accent:#6366f1;--radius:16px;--speed:.2s;}
body.light{--bg:#f6f6f9;--card:#fff;--text:#161616;}
body{background:var(--bg);color:var(--text);font-family:Inter,system-ui,sans-serif;margin:0;padding:40px 20px;opacity:0;animation:fadeIn .4s ease forwards;}
@keyframes fadeIn{to{opacity:1;}}
h1{font-size:32px;margin-bottom:20px;font-weight:600;text-align:center;}
.controls{display:flex;justify-content:center;gap:10px;margin-bottom:30px;flex-wrap:wrap;}
button, select, input{background:var(--card);color:var(--text);border:1px solid var(--accent);padding:10px 14px;border-radius:var(--radius);font-size:15px;cursor:pointer;transition:.2s;}
input{width:200px;}
.grid{display:grid;gap:20px;grid-template-columns:repeat(auto-fit,minmax(250px,1fr));max-width:1200px;margin:0 auto;}
.section-title{margin:40px auto 15px;max-width:1200px;font-size:20px;opacity:.7;font-weight:500;}
a{text-decoration:none;}
.item{background:var(--card);padding:24px;border-radius:var(--radius);color:var(--text);display:flex;justify-content:space-between;align-items:center;border:2px solid transparent;transition:.2s;position:relative;overflow:hidden;}
.item:hover{border-color:var(--accent);transform:translateY(-4px);}
.left{display:flex;gap:14px;align-items:center;}
.icon svg{width:28px;height:28px;opacity:.8;}
.name{font-size:18px;font-weight:600;}
.files{opacity:.5;font-size:14px;}
.preview{position:absolute;left:0;top:100%;width:100%;padding:12px;background:var(--card);border-top:1px solid var(--accent);opacity:0;pointer-events:none;transition:.2s;}
.item:hover .preview{top:100%;opacity:1;}
.action-btn{margin-left:8px;font-size:13px;padding:4px 6px;cursor:pointer;border-radius:8px;background:var(--accent);color:#fff;opacity:.8;transition:.2s;}
.action-btn:hover{opacity:1;}
</style>
</head>
<body>
<h1>Projects</h1>

<div class="controls">
    <input type="text" id="search" placeholder="Search...">
    <select id="sort">
        <option value="az">A → Z</option>
        <option value="za">Z → A</option>
        <option value="new">Date: Newest</option>
        <option value="old">Date: Oldest</option>
    </select>
    <button id="mode">Light/Dark</button>
    <button onclick="createFolder()">+ New Folder</button>
</div>

<div class="section-title">Folders</div>
<div class="grid" id="grid">
<?php foreach($data as $dir): ?>
    <div class="item" data-name="<?= strtolower($dir['name']) ?>" data-date="<?= $dir['mtime'] ?>">
        <a href="<?= htmlspecialchars($dir['name']) ?>" style="flex:1; display:flex; text-decoration:none; color:inherit;">
            <div class="left">
                <div class="icon"><svg fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path d="M3 7l2-2h6l2 2h8v12H3z" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
                </svg></div>
                <div>
                    <div class="name"><?= $dir['name'] ?></div>
                    <div class="files"><?= $dir['files'] ?> files</div>
                </div>
            </div>
        </a>
        <div>
            <span class="action-btn" onclick="renameItem('<?= $dir['name'] ?>')">Rename</span>
            <span class="action-btn" onclick="deleteItem('<?= $dir['name'] ?>')">Delete</span>
        </div>
        <?php if($dir['preview']): ?>
            <div class="preview"><?= $dir['preview'] ?></div>
        <?php endif; ?>
    </div>
<?php endforeach; ?>
</div>

<div class="section-title">Files</div>
<div class="grid" id="filegrid">
<?php foreach($fileData as $file): ?>
    <div class="item" data-name="<?= strtolower($file['name']) ?>" data-date="<?= $file['mtime'] ?>">
        <div class="left">
            <div class="icon"><svg fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path d="M14 2H6a2 2 0 0 0-2 2v16
                         a2 2 0 0 0 2 2h12
                         a2 2 0 0 0 2-2V8z"
                      stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
            </svg></div>
            <div>
                <div class="name"><?= $file['name'] ?></div>
                <div class="files"><?= round($file['size']/1024,1) ?> KB</div>
            </div>
        </div>
        <div>
            <span class="action-btn" onclick="renameItem('<?= $file['name'] ?>')">Rename</span>
            <span class="action-btn" onclick="deleteItem('<?= $file['name'] ?>')">Delete</span>
        </div>
    </div>
<?php endforeach; ?>
</div>

<script>
const grid = document.getElementById('grid');
const fgrid = document.getElementById('filegrid');
const items = [...grid.children,...fgrid.children];
const search = document.getElementById('search');
const sort = document.getElementById('sort');
const mode = document.getElementById('mode');

// THEME
if(localStorage.theme==='light')document.body.classList.add('light');
mode.onclick=()=>{document.body.classList.toggle('light');localStorage.theme=document.body.classList.contains('light')?'light':'dark';}

// SEARCH
search.addEventListener('input',()=>{let q=search.value.toLowerCase();items.forEach(i=>{let n=i.dataset.name;i.style.display=n.includes(q)?'':'none';});});

// SORT
sort.addEventListener('change',()=>{
    let type=sort.value;
    [grid,fgrid].forEach(section=>{
        let arr=[...section.children];
        if(type==='az')arr.sort((a,b)=>a.textContent.localeCompare(b.textContent));
        if(type==='za')arr.sort((a,b)=>b.textContent.localeCompare(a.textContent));
        if(type==='new')arr.sort((a,b)=>b.dataset.date-a.dataset.date);
        if(type==='old')arr.sort((a,b)=>a.dataset.date-b.dataset.date);
        section.innerHTML='';arr.forEach(e=>section.appendChild(e));
    });
});

// CREATE FOLDER
function createFolder(){
    let name=prompt('Folder name:');
    if(name)fetch('',{method:'POST',headers:{'Content-Type':'application/x-www-form-urlencoded'},body:'action=create&name='+encodeURIComponent(name)})
    .then(r=>r.json()).then(r=>r.success?location.reload():alert(r.error));
}

// RENAME
function renameItem(oldName){
    let newName=prompt('New name:',oldName);
    if(newName && newName!==oldName)fetch('',{method:'POST',headers:{'Content-Type':'application/x-www-form-urlencoded'},body:'action=rename&old='+encodeURIComponent(oldName)+'&new='+encodeURIComponent(newName)})
    .then(r=>r.json()).then(r=>r.success?location.reload():alert(r.error));
}

// DELETE
function deleteItem(target){
    if(confirm('Are you sure to delete '+target+'?'))fetch('',{method:'POST',headers:{'Content-Type':'application/x-www-form-urlencoded'},body:'action=delete&target='+encodeURIComponent(target)})
    .then(r=>r.json()).then(r=>r.success?location.reload():alert(r.error));
}
</script>

</body>
</html>
