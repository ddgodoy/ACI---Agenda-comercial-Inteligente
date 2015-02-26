define(["jquery", "backbone"], function($, Backbone) {



    Component = Backbone.Model.extend({
        defaults: {
            name: 'component'
        },
        initialize: function() {
            console.info('iniciando component');
        }
    });


    Page = Component.extend({
        defaults: {
            id: '1',
            name: 'page',
            type: 'page',
            url_save: '',
            edit_html: '',
            html: ''
        },
        initialize: function() {
            this.set('assets', new AssetCollection());
            this.set('edit_html',$("#page_container"));
        }
    });

    Asset = Backbone.Model.extend({
        default: {
            name: '',
            bundle: ''
        }
    });

    Link = Backbone.Model.extend({});

    AssetCollection = Backbone.Collection.extend({
        model: Asset
    });
    
    assets = new AssetCollection();


    Imagen = Component.extend({
        defaults: {
            type: 'image',
            id: '',
            src: ''
        }
    });

});
