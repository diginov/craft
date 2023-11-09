module.exports = {

    // https://tailwindcss.com/docs/configuration#selector-strategy
    important: '.tailwind',

    // https://tailwindcss.com/docs/content-configuration#configuring-source-paths
    content: [
        "./src/**/*.{js,vue}",
        "./../../templates/**/*.{html,twig}",
    ],

    // https://tailwindcss.com/docs/content-configuration#safelisting-classes
    safelist: [
        "status",
        "tailwind",
    ],

    // https://tailwindcss.com/docs/configuration#core-plugins
    corePlugins: {
        preflight: false,
    },

};
