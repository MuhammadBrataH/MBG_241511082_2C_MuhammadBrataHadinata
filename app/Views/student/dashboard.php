<div class="card" style="max-width:500px; margin:0 auto; text-align:center;">
	<h2 style="color:#007bff; margin-top:0;">Student Dashboard</h2>
	<p style="font-size:1.1rem;">Halo, <b><?= session()->get('username') ?></b>! Anda login sebagai <span style="color:#007bff;"><b><?= session()->get('role') ?></b></span></p>
	<a href="/logout" class="btn">Logout</a>
</div>
