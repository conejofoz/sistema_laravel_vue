@extends('principal')
@section('contenido')
<template v-if="menu==0">
    <h1>Conteúdo do menu 0</h1>
</template>

<template v-if="menu==1">
    <categoria></categoria>
</template>
<template v-if="menu==2">
    <articulo></articulo>
</template>
<template v-if="menu==3">
    <h1>Conteúdo do menu 3</h1>
</template>
@endsection