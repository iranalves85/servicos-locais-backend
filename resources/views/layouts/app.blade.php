<!DOCTYPE html>
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale = 1.0, maximum-scale=1.0, user-scalable=no">
    <meta name="csrf_token" content="<?php echo csrf_token() ?>" />
    <link rel="profile" href="http://gmpg.org/xfn/11" />
    <link rel="stylesheet" href="assets/style.css" />
    <link rel="stylesheet" href="https://fonts.googleapis.com/icon?family=Material+Icons">
    <title>App Name - @yield('title')</title>
</head>

<body>

    @include('components.topapp')

    @yield('content')

    <footer class="text-center p-4 bg-preto font-branca font-size-7">

        <div class="container">
            <div class="row justify-content-center">

                <div class="col-12 text-left">
                    <p><strong>Importante:</strong> Esse projeto tem como objetivo divulgar de maneira mais transparente
                        a situação de recursos e equipamentos em época de pandemia. Os responsáveis ou funcionários
                        dessas instituições podem cadastrar a quantidade restante ou mesmo a falta completa de recursos,
                        com isso, empresas que estão dispostas a ajudar podem direcionar seus esforços a qual ela
                        escolher.</p>
                    <p>
                        Criado por <a href="https://makingpie.com.br">Making Pie - Desenvolvimento Web & Marketing
                            Digital
                        </a> - 2020
                    </p>
                </div>


            </div>
        </div>

    </footer>

    <script src="assets/scripts.js"></script>

</body>

</html>