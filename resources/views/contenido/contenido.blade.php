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
<template v-if="menu==4">
    <proveedor></proveedor>
</template>
<template v-if="menu==6">
    <cliente></cliente>
</template>
<template v-if="menu==7">
    <user></user>
</template>
<template v-if="menu==8">
    <rol></rol>
</template>
@endsection