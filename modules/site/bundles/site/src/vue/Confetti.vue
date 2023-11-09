<template>
    <main></main>
</template>

<script>
import VueConfetti from "vue-confetti";

Vue.use(VueConfetti);

export default {
    mounted: function () {
        this.$confetti.start({
            particles: [
                {
                    type: "heart",
                },
                {
                    type: "circle",
                },
            ],
            defaultColors: ["red", "pink", "#ba0000"],
        });
        setTimeout(() => {
            this.$confetti.stop();
        }, 5000);
    },
    methods: {},
};
</script>
