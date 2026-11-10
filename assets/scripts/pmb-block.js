var el = wp.element.createElement,
    registerBlockType = wp.blocks.registerBlockType,
    ServerSideRender = wp.components.ServerSideRender;

registerBlockType( 'avpvh-print-website/setupform', {
    title: 'AV-PvH Print Website',

    icon: 'universal-access-alt',

    category: 'layout',

    edit: function(props) {
        return (
            el(ServerSideRender, {
                block: "avpvh-print-website/setupform",
                attributes: props.attributes
            })
        );
    },

    save: function() {
        return null;
    },
} );

