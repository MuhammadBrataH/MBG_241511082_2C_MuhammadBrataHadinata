<div class="card" style="background:linear-gradient(120deg,#e0eaff 0%,#f8f9fa 100%);box-shadow:0 4px 16px rgba(0,123,255,0.08);">
    <div style="display:flex;align-items:center;justify-content:space-between;">
        <h2 style="margin-top:0; color:#007bff;display:flex;align-items:center;gap:10px;">
            <img src="https://img.icons8.com/color/48/000000/admin-settings-male.png" style="vertical-align:middle;"> Admin Dashboard
        </h2>
        <span style="background:#007bff;color:#fff;padding:6px 18px;border-radius:20px;font-weight:500;">Admin</span>
    </div>
    <h3 style="margin-bottom:10px;color:#0056b3;">Tambah Course</h3>
    <form method="post" action="/admin/addCourse" style="display:grid;grid-template-columns:1fr 1fr;gap:12px;max-width:500px;margin:0 auto;">
        <input type="text" name="course_code" placeholder="Kode" required style="margin-bottom:8px;">
        <input type="text" name="course_name" placeholder="Nama" required style="margin-bottom:8px;">
        <textarea name="description" placeholder="Deskripsi" style="margin-bottom:8px;grid-column:span 2;"></textarea>
        <button type="submit" class="btn" style="grid-column:span 2;">Tambah</button>
    </form>
</div>

<div class="card" style="background:linear-gradient(120deg,#f8f9fa 0%,#e0eaff 100%);">
    <h3 style="margin-top:0; color:#007bff;display:flex;align-items:center;gap:8px;">
        <img src="https://img.icons8.com/color/32/000000/classroom.png"> Daftar Courses
    </h3>
    <table class="table">
        <tr>
            <th>Kode</th>
            <th>Nama</th>
            <th>Deskripsi</th>
        </tr>
        <?php foreach($courses as $c): ?>
        <tr style="background:#f4faff;">
            <td><b><?= $c['course_code'] ?></b></td>
            <td><?= $c['course_name'] ?></td>
            <td><?= $c['description'] ?></td>
        </tr>
        <?php endforeach; ?>
    </table>
</div>

<div class="card" style="background:linear-gradient(120deg,#e0eaff 0%,#f8f9fa 100%);">
    <h3 style="margin-top:0; color:#007bff;display:flex;align-items:center;gap:8px;">
        <img src="https://img.icons8.com/color/32/000000/student-male--v2.png"> Daftar Students
    </h3>
    <table class="table">
        <tr>
            <th>Username</th>
            <th>NIM</th>
        </tr>
        <?php foreach($students as $s): ?>
        <tr>
            <td><b><?= $s['username'] ?></b></td>
            <td><?= $s['nim'] ?? 'N/A' ?></td>
        </tr>
        <?php endforeach; ?>
    </table>
</div>
