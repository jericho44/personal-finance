<template>
    <div>
        <div class="loader-component" />
    </div>
</template>

<script>
export default {
    name: 'LoaderComponent',
}

</script>

<style scoped>
.loader-component {
    border: 5px solid #3E9DF6;
    border-radius: 50%;
    border-top: 5px solid #f2f2f2;
    width: 50px;
    height: 50px;
    -webkit-animation: spin 2s linear infinite;

    animation: spin 2s linear infinite;
}

/* Safari */
@-webkit-keyframes spin {
    0% {
        -webkit-transform: rotate(0deg);
    }

    100% {
        -webkit-transform: rotate(360deg);
    }
}

@keyframes spin {
    0% {
        transform: rotate(0deg);
    }

    100% {
        transform: rotate(360deg);
    }
}
</style>
