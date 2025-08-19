# ใช้หลังจาก git clone แล้ว cd เข้ามาใน repo

# ดึง branch ทั้งหมดจาก remote
git fetch --all

# ดึงชื่อ remote branch ทั้งหมด ยกเว้น HEAD
$remoteBranches = git branch -r | Where-Object {$_ -notmatch "->"}

foreach ($remote in $remoteBranches) {
    $branch = $remote.Trim() -replace "origin/", ""
    # ถ้า local branch ไม่มี branch นี้
    if (-not (git show-ref --verify --quiet "refs/heads/$branch")) {
        git branch --track $branch $remote
        Write-Output "✅ Created local branch '$branch' tracking '$remote'"
    }
    else {
        Write-Output "ℹ️ Local branch '$branch' already exists, skipped"
    }
}
#**Run** .\setup-tracking.ps1