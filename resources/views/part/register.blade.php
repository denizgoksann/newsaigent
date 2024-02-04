<div class="col-12 col-md-6 mt-5">
    <form id="registerForm">
        @csrf

        <h1>Kayıt Ol</h1>
        <div class="mb-3">
            <label for="" class="form-label">Name</label>
            <input type="text" name="name" id="name_register" class="form-control">
        </div>
        <div class="mb-3">
            <label for="" class="form-label">E-Mail Adresi</label>
            <input type="email" name="email" id="email_register" class="form-control">
        </div>
        <div class="mb-3">
            <label for="" class="form-label">Şifre</label>
            <input type="password" name="password" id="password_register" class="form-control">
        </div>
        <div class="mb-3">
            <label for="" class="form-label">Şifre Tekrar</label>
            <input type="password" name="password_again" id="password_again" class="form-control">
        </div>
        <button type="button" class="btn btn-userForm" id="registerFormButton">Kayıt Ol</button>
     </form>
</div>
