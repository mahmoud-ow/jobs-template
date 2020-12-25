<!DOCTYPE html>
<html dir="<?php echo App::getLocale()=='ar'? 'rtl' : 'ltr' ?>"
  lang="<?php echo App::getLocale()=='ar'? 'ar' : 'en' ?>">

<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <meta name="csrf-token" content="{{ csrf_token() }}">

  <link rel="stylesheet" href="{{ asset('css/app.css') }}">

  {{-- icons --}}
  <script src='https://kit.fontawesome.com/a076d05399.js'></script>

  <!-- Fonts -->
  @if( Config::get('app.locale') == 'en' )
  <link href="https://fonts.googleapis.com/css2?family=Nunito:wght@400;600;700&display=swap" rel="stylesheet">
  @else
  <link href="https://fonts.googleapis.com/css2?family=Almarai&display=swap" rel="stylesheet">
  @endif

  <style>
    div,
    h1,
    h2,
    h3,
    h4,
    h5,
    h6,
    input,
    select,
    textarea,
    p,
    small,
    td,
    th {
      font-family: 'Nunito', 'Almarai', sans-serif
    }
  </style>


</head>

<body>

  <div id="app">
    <div class="content">
      <chat-component></chat-component>
      <router-view></router-view>
    </div>{{-- /.content --}}
  </div>
  <!-- /#app -->
  <script>

    window.userInfo = JSON.parse('{!! json_encode($user) !!}');
    window.lang = '{!! Config::get("app.locale") !!}'
    window.csrf = document.querySelector('meta[name="csrf-token"]').content
    
    

  </script>


  <script src="{{ asset('js/app.js') }}"></script>

  




</body>

</html>