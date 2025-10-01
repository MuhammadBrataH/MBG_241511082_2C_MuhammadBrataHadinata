<div class="card" style="background:linear-gradient(120deg,#f8f9fa 0%,#e0eaff 100%);">
    <h2 style="color:#007bff; margin-top:0;display:flex;align-items:center;gap:10px;">
        <img src="https://img.icons8.com/color/32/000000/classroom.png"> Daftar Courses
    </h2>
    <?php if(session()->getFlashdata('success')): ?>
        <p style="color:green; font-weight:500; margin-bottom:12px;">✅ <?= session()->getFlashdata('success') ?></p>
    <?php endif; ?>
    <table class="table">
        <tr>
            <th>Kode</th>
            <th>Nama</th>
            <th>Deskripsi</th>
            <th>Aksi</th>
        </tr>
        <?php foreach($courses as $c): ?>
        <tr style="background:#f4faff;">
            <td><b><?= $c['course_code'] ?></b></td>
            <td><?= $c['course_name'] ?></td>
            <td><?= $c['description'] ?></td>
            <td>
                <a href="/student/enroll/<?= $c['id'] ?>" class="btn" style="font-weight:500;">Enroll</a>
            </td>
        </tr>
        <?php endforeach; ?>
    </table>
</div>
