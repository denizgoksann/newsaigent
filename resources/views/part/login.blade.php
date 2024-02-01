<div class="col-12 col-md-6 mt-5">
    <form id="loginForm">
        @csrf

        <h1>GİRİŞ YAPIN</h1>
        <div class="mb-3">
            <label for="" class="form-label">E-Mail Adresi</label>
            <input type="email" name="email" id="email" class="form-control">
        </div>
        <div class="mb-3">
            <label for="" class="form-label">Şifre</label>
            <input type="password" name="password" id="password" class="form-control">
        </div>
        <button type="button" class="btn btn-userForm" id="loginFormButton">Giriş Yap</button>

     </form>
</div>