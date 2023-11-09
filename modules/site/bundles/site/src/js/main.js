import $ from "jquery";
import Craft from "@craft/cp";
import Garnish from "@garnish/garnish";

import Confetti from "@/vue/Confetti.vue";
import Vue from "vue";

Garnish.$doc.ready(function () {

    /**
     * Testing vue component
     */
    let confettiApp = new Vue({
        el: "#test-vue-confetti",
        components: {
            confetti: Confetti,
        },
    });

});
