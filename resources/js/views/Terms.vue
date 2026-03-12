<template>
    <div>
        <div class="d-flex justify-content-center p-3" style="border-bottom: 1px #ddd solid;">
            <img :src="logo" style="width:150px">
        </div>
        <div class="container">
            <div class="row justify-content-center">
                <div class="col-lg-6">
                    <h2 class="text-center pt-3">Terms and Conditions</h2>
                    <div class="text-center pt-1 text-gray-700">Daftar pegawai pada Krakatau Sarana Infrastruktur</div>
                    <div class="text-center pt-5">Lorem Ipsum is simply dummy text of the printing and typesetting
                        industry. Lorem Ipsum has been the
                        industry's standard dummy text ever since the 1500s, when an unknown printer took a galley of
                        type and
                        scrambled it to make a type specimen book. It has survived not only five centuries, but also the
                        leap
                        into electronic typesetting, remaining essentially unchanged. It was popularised in the 1960s
                        with the
                        release of Letraset sheets containing Lorem Ipsum passages, and more recently with desktop
                        publishing
                        software like Aldus PageMaker including versions of Lorem Ipsum.</div>

                    <div class="text-center pt-5">Lorem Ipsum is simply dummy text of the printing and typesetting
                        industry. Lorem Ipsum has been the
                        industry's standard dummy text ever since the 1500s, when an unknown printer took a galley of
                        type and
                        scrambled it to make a type specimen book. It has survived not only five centuries, but also the
                        leap
                        into electronic typesetting, remaining essentially unchanged. It was popularised in the 1960s
                        with the
                        release of Letraset sheets containing Lorem Ipsum passages, and more recently with desktop
                        publishing
                        software like Aldus PageMaker including versions of Lorem Ipsum.</div>
                    <div class="d-flex justify-content-center">
                        <button class="btn mt-5 mb-5 btn-primary" style="background-color: #00A1D3">Accept</button>
                    </div>
                </div>
            </div>

        </div>
    </div>
</template>

<script>
export default {
    data() {
        return {
            logo: this.$assetUrl() + 'extends/images/logo.png',
        }
    },
    mounted() { }
}

</script>
