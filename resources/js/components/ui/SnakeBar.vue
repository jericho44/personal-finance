<template>
    <div>
        <div :class="`${isActive ? '' : 'd-none'} backdrop-snakebar`"
            :style="`position:fixed; top:0; right:0; left:0; bottom:0; background-color:rgba(0,0,0,0.3); z-index:999${zIndex || 0};`"
            @click="$emit('update:is-active', false); $emit('click-backdrop');" />
        <div class="wrap"
            :style="`height: ${isActive ? height : 0};overflow-y:${scrollAll ? 'auto' : ''}; position:fixed; z-index:99999 !important; bottom:0; left:5px; right:5px; border-top-left-radius:35px; border-top-right-radius:35px; box-shadow:0px 0px 5px lightgray; background-color:white;`">
            <div @click="$emit('update:is-active', false)" class="icon-dash m-auto"
                style="cursor:pointer; width:50px; height:5px; border-radius:100px; background-color:lightgray; position:absolute; top:10px; left:0; right:0;" />
            <br><br>
            <div class="konten px-7">
                <slot />
            </div>
        </div>
    </div>
</template>

<script>
export default {
    props: {
        scrollAll: {
            type: Boolean,
            default: true
        },
        isActive: {
            type: Boolean,
            default: false
        },
        height: {
            type: String,
            default: `0`
        },
        zIndex: {
            type: String,
            default: `0`
        }
    },
    data() {
        return {

        }
    },
    methods: {

    }
}
</script>

<style scoped>
.wrap {
    transition: all 0.4s !important;
}

.backdrop-snakebar {
    transition: all 2s !important;
}
</style>
