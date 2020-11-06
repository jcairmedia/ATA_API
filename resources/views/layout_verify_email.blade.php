<style>
    .color-main {
        color: #6fb258;
    }
    
    .text-center {
        text-align: center
    }
    
    .box {
        border: 1px solid #6fb258;
        padding: 70px;
    }
    
    .main {
        padding-left: 20%;
        padding-right: 20%;
    }
</style>
<div class="main">
    <img src="" alt="">

    <h1 class="text-center">En ATA estamos para escucharte y ayudarte en cualquier problema legal que tengas</h1>
    <div class="box">
        <p class="text-center">
            Hola {{ $customer_name }} gracias por registrarte en Abogados a tu Alcance
        </p>
        <p class="text-center">Por favor confirma tu correo electrónico</p>
        <p class="text-center">Para ello simplemente debes hacer click en el siguiente enlace</p>
        <a href="{{url('register/verify', $confirmation_code)}}">Clic para confirmar tu email</a>
    </div>
    <p class="text-center">
        <a href="" class="color-main text-center">Aviso de privacidad</a>
    </p>
</div>