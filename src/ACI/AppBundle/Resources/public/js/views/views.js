define(["jquery", "underscore", "backbone", "blockui", "bootstrap", "backbone_localstorage", "backbone_undo", "resize", "toastr", "colorpicker", "transition", "slimscroll"], function ($, _, Backbone, bootstrap, toastr, transition, crop, slimscroll) {

    var current_link = null;
    var carrusel = false;
    var dragging = false;
    var resizing = false;
    var dropelement;
    var dropside;
    var color_element;
    var boxHelper = false;
    var section;
    var reducedelement;
    var originalsize = "";


    function lg(log) {
        console.log(log);
    }

    $("#colorText").colorpicker();

    $('.setbodycolor').colorpicker().on('changeColor', function (ev) {
        color = ev.color.toRGB();
        $("body").css("background-color", "rgba(" + color.r + "," + color.g + "," + color.b + "," + color.a + ")");
    });

    $('.setcontainercolor').colorpicker().on('changeColor', function (ev) {
        color = ev.color.toRGB();
        $("#fullpage").css("background-color", "rgba(" + color.r + "," + color.g + "," + color.b + "," + color.a + ")");
        $("#boxedbg").attr("data-contcolor", $("#fullpage").css("background-color"));

    });

    function menuHider() {
        if ($("#side-opt-boxed").hasClass("active")) {
            $("#boxed div").removeClass("pressedBoxed");
            $("#side-opt-boxed").animate({
                right: "-=182",
                opacity: "hide"
            }, 300, function () {
            });
            $("#side-opt-boxed").removeClass("active");
        }

        if ($("#side-opt-fullwidth").hasClass("active")) {
            $("#fullwidth div").removeClass("pressedFull");
            $("#side-opt-fullwidth").animate({
                right: "-=182",
                opacity: "hide"
            }, 300, function () {
            });
            $("#side-opt-fullwidth").removeClass("active");
        }
    }

    function removeColClass(items) {
        for (l = 1; l <= 12; l++) {
            items.removeClass("col-md-" + l);
        }
    }

    function getColSize(elem) {
        var tamactual = 0;
        for (k = 1; k <= 12; k++) {
            if (elem.hasClass("col-md-" + k)) {
                tamactual = k;
            }
        }
        return tamactual;
    }

    function getRowSize(row) {
        var widthelems = 0;

        items = row.children(".element").toArray();

        for (m = 0; m < items.length; m++) {
            for (j = 1; j <= 12; j++) {

                if ($(items[m]).hasClass("col-md-" + j)) {
                    widthelems = widthelems + j;
                }
            }
        }
        return widthelems;
    }

    function compactRow(row) {
        var i, coltoreduce;
        var childs = row.children();
        var childsnum = childs.length;

        $(childs).each(function (i) {
            if (getColSize($(this)) > 1)
                coltoreduce = i;
        });
        return coltoreduce;
    }

    function drop_extra(ui, elem) {
        if (ui.helper.attr('data-nombre') == 'borde') {

            var borde = ui.helper.attr('data-borde');
            elem.addClass(borde);
            elem.attr('data-borde', borde);

        }

        if (ui.helper.attr('data-nombre') == 'animacion') {
            if (elem.attr('data-animacion'))
                elem.removeClass(elem.attr('data-animacion'));

            var animacion = ui.helper.attr('data-animacion');
            elem.addClass(animacion);
            elem.attr('data-animacion', animacion);
        }

        if (ui.helper.attr('data-nombre') == 'color') {

            currentColor = $(event.target).css("background-color");

            $("#modal_color").modal();
            //$('#colorText').colorpicker('setValue', currentColor);

            color_element = elem;
        }
    }

    function init_drop_boxes() {

        if ($("#page_container").children().length == 0)
            $("#page_container").append('<div class="drop_init drop_content"></div>');

        if ($("#header_container").children().length == 0)
            $("#header_container").append('<div class="drop_init drop_header"></div>');

        if ($("#footer_container").children().length == 0)
            $("#footer_container").append('<div class="drop_init drop_footer"></div>');


        $(".drop_init").droppable({
            accept: ".box",
            over: function (event, ui) {
                dropside = "";
                ui.draggable.data("current-droppable", $(event.target));
            },
            drop: function (e, ui) {
                if (ui.draggable.hasClass("element")) {
                    parent = $(this).parent();
                    $(this).parent().html(ui.draggable);
                    ui.draggable.wrap("<div class='row'></div>");
                    ui.draggable.addClass("compToolshow");

                }
                else {
                    clon = ui.draggable.clone()
                    clon.addClass("element col-md-12").insertBefore($(e.target)).addClass("element").wrap("<div class='row'></div>");
                    clon.children(".componentToolbar").addClass("compToolshow");
                    init_rowcontol();
                    $(e.target).remove();
                }

                $("#fullpage .row").each(function () {
                    if ($(this).children(".element").length == 0)
                        $(this).remove();
                });

                init_drop_boxes();


            }

        });

    }

    function resizeRules(ui) {
        item = ui.element;

        var size_son = item.width();
        var size_parent = item.parent().parent().width();
        var ancho_columna = size_parent / 12;

        var clases = item.parent().attr("class");

        var widthelems = 0;
        var tamactual = 0;

        for (i = 1; i <= 12; i++) {
            if (item.parent().hasClass("col-md-" + i)) {
                tamactual = i;
            }
        }

        $.each(item.parent().parent().children(), function () {
            for (i = 1; i <= 12; i++) {
                if ($(this).hasClass("col-md-" + i)) {
                    widthelems = widthelems + i;
                }
            }
        })

        if (widthelems <= 12) {
            widthelems = 0;
            var col = 0;

            for (i = 1; i <= 12; i++) {
                if (size_son < i * ancho_columna + 55 && size_son > ancho_columna * i - 55) {
                    col = i;
                }
            }

            $.each(item.parent().parent().children(), function () {
                for (i = 1; i <= 12; i++) {
                    if ($(this).hasClass("col-md-" + i)) {
                        widthelems = widthelems + i;
                    }
                }
            })

            if ((widthelems - tamactual + col) <= 12) {

                for (i = 1; i <= 12; i++) {
                    item.parent().removeClass("col-md-" + i);
                }

                clases = item.parent().attr("class");

                if (col == 0) {
                    item.parent().attr("class", clases + " col-md-" + tamactual);

                }
                else
                    item.parent().addClass("col-md-" + col);

            }

        }

    }

    function resizeRules2(ui) {
        item = ui.element;

        var size_son = item.width();
        var size_parent = item.parent().parent().width();
        var ancho_columna = size_parent / 12;

        var clases = item.parent().attr("class");


        var widthelems = 0;
        var tamactual = 0;
        var tamsiguiente = 0;

        tamactual = getColSize(item.parent());
        widthelems = getRowSize(item.parent().parent());

        widthelems = 0;
        var col = 0;

        for (i = 1; i <= 12; i++) {
            if (size_son < i * ancho_columna + 55 && size_son > ancho_columna * i - 55) {
                col = i;
            }
        }

        $.each(item.parent().parent().children(), function () {
            for (i = 1; i <= 12; i++) {
                if ($(this).hasClass("col-md-" + i)) {
                    widthelems = widthelems + i;
                }
            }
        })

        for (i = 1; i <= 12; i++) {
            item.parent().removeClass("col-md-" + i);
        }

        clases = item.parent().attr("class");

        if (col == 0) {
            item.parent().attr("class", clases + " col-md-" + tamactual);

        }
        else {
            if (item.parent().next()) {
                for (i = 1; i <= 12; i++) {
                    if (item.parent().next().hasClass("col-md-" + i))
                        tamsiguiente = i;
                }


                diferencia = tamactual - col;
                //console.log(diferencia);
                item.parent().next().removeClass("col-md-" + tamsiguiente);

                //Disminuyo el tamño de la columna o se quedo igual
                newtam = tamsiguiente + diferencia;
                //vrrr = col + newtam;console.log(vrrr);

                if (newtam > 0 && newtam < 12) {
                    item.parent().next().addClass("col-md-" + newtam);
                    item.parent().addClass("col-md-" + col);
                }
                else {
                    item.parent().next().addClass("col-md-" + tamsiguiente);
                    item.parent().addClass("col-md-" + tamactual);
                }


            }

            else {
                item.parent().addClass("col-md-" + col);
            }


        }

    }

    function init_rowcontol() {
        $(".row").droppable({
            accept: ".box",
            over: function (event, ui) {
                items = $(event.target).children(".element");
                numelements = items.length;

                if ($(event.target).has(ui.draggable).length > 0)
                    numelements = numelements - 1;


                if (numelements == 12) {
                    $(event.target).addClass("dropLimit");
                }
            },
            out: function (event, ui) {
                $(event.target).removeClass("dropLimit");

            },
            drop: function (event, ui) {
                $(event.target).removeClass("dropLimit");


            }
        });

    }

    //Determina en que posición esta el mouse cuando se drgaea un objeto sobre una zona dropeable y muestra una barra azul indicando dónde se dropeara el objeto
    function light_drag(drag, e) {
        //Es el elemento sobre el cual esta el objeto drageado aplica solo a .elements
        var droppable = drag.data("current-droppable");
        var alto, ancho, offset, x, y, reduceindex, reducesize, tempdrop;


        $("#black_layer_header,#black_layer_content,#black_layer_footer").children("span").removeClass("overlay_section");

        if (droppable) {
            section = droppable.parent().parent().attr("id");

            if (section == "page_container") {
                $("#black_layer_header,#black_layer_footer").children("span").addClass("overlay_section");
            }

            if (section == "header_container") {
                $("#black_layer_content,#black_layer_footer").children("span").addClass("overlay_section");
            }

            if (section == "footer_container") {
                $("#black_layer_header,#black_layer_content").children("span").addClass("overlay_section");
            }


            if (droppable.hasClass("drop_init")) {
                //Es una caja de drop
                dropelement = droppable;

                section = droppable.parent().attr("id");

                if (section == "page_container") {
                    $("#black_layer_header,#black_layer_footer").children("span").addClass("overlay_section");

                }

                if (section == "header_container") {
                    $("#black_layer_content,#black_layer_footer").children("span").addClass("overlay_section");
                }

                if (section == "footer_container") {
                    $("#black_layer_header,#black_layer_content").children("span").addClass("overlay_section");
                }

            }

            else {
                //Es un elemento
                var alto = droppable.height();
                var ancho = droppable.width();
                var offset = droppable.offset(),
                    x = e.pageX - offset.left,
                    y = e.pageY - offset.top;


                var tol_alto = 30;
                var tol_ancho = 30;

                if (dropelement) {
                    if (dropelement != droppable) {
                        boxHelper = false;
                        $("#boxHelper").remove();
                    }
                }

                dropelement = droppable;
                tempdrop = dropside;


                items = dropelement.parent().children(".element");
                numelements = items.length;
                var parentHeight = 0;

                if (dropelement.parent().has($(e.target)).length > 0)
                    numelements = numelements - 1;


                if (y > alto - tol_alto) {
                    dropside = "muyabajo";
                }
                else if (y < tol_alto) {
                    dropside = "muyarriba";
                }

                else if (x > ancho - 50 && x < ancho + 150) {
                    if (numelements != 12) {
                        dropside = "derecha";
                    }
                }
                else if (x < 50 && x > -150) {
                    if (numelements != 12) {
                        dropside = "izquierda";

                    }
                }
                else {
                    dropside = "nada";
                }

                if (tempdrop != dropside) {
                    boxHelper = false;
                    $("#boxHelper").remove();
                    if (originalsize != "" && reducedelement != "") {
                        removeColClass(reducedelement);
                        reducedelement.addClass("col-md-" + originalsize);

                    }

                }

                if (!boxHelper) {
                    if (dropside == "izquierda" || dropside == "derecha") {
                        parentHeight = dropelement.parent().height() + 20;
                        reducedcol = false;
                        if (getRowSize(dropelement.parent()) == 12) {
                            reducedcol = true;
                            reduceindex = compactRow(dropelement.parent());
                            originalsize = getColSize($(items[reduceindex]));
                            reducedelement = $(items[reduceindex]);
                            console.log(reduceindex);
                            removeColClass($(items[reduceindex]));
                            reducesize = originalsize - 1;
                            $(items[reduceindex]).addClass("col-md-" + reducesize);
                        }
                        boxHelper = true;
                        if (dropside == "derecha")
                            dropelement.after("<div id='boxHelper' class='boxHelper_col col-md-1' style='height:" + parentHeight + "px'></div>");
                        else
                            dropelement.before("<div id='boxHelper' class='boxHelper_col col-md-1' style='height:" + parentHeight + "px '></div>");

                    }

                    if (dropside == "muyarriba" || dropside == "muyabajo") {

                        if (!boxHelper) {
                            boxHelper = true;
                            if (dropside == "muyarriba")
                                $("<div id='boxHelper' class='row boxHelper_row'></div>").insertBefore(dropelement.parent());
                            else
                                $("<div id='boxHelper' class='row boxHelper_row'></div>").insertAfter(dropelement.parent());
                        }

                        //todo: Agregar a abajo
                        //dropelement.append("<div id='boxHelper' class='row boxHelper_row'></div>");

                    }

                    if (dropside == "nada") {
                        boxHelper = false;
                        $("#boxHelper").remove();
                    }

                }


            }
        }

    }

    function map_drop(draged) {
        var items = dropelement.parent().children(".element");
        var coldraged;
        numelements = items.length;

        var $clon = draged.clone();
        removeColClass(draged);

        if (dropside == "muyarriba" || dropside == "muyabajo") {
            init_rowcontol();
            if (draged.hasClass("element")) {
                row = draged.parent();
                draged.remove();
                if (row.children(".element").length == 0)
                    row.remove();
            }

            if (dropside == "muyarriba") {
                $clon.addClass("element").insertBefore(dropelement.parent()).addClass("element").wrap("<div class='row'></div>");
                if (getColSize($clon) == 0)
                    $clon.addClass("col-md-12");
            }
            else {
                $clon.addClass("element").insertAfter(dropelement.parent()).addClass("element").wrap("<div class='row'></div>");
                if (getColSize($clon) == 0)
                    $clon.addClass("col-md-12");
            }

        }

        /*  if(dropside=="arriba" || dropside=="abajo"){

         if(dropelement.parent().hasClass("wrapelem")){
         if(dropside=="arriba")
         $clon.addClass("element").insertBefore(dropelement);
         else
         $clon.addClass("element").insertAfter(dropelement);
         }

         else {
         for (i = 1; i <= 12; i++) {
         if (dropelement.hasClass("col-md-" + i)) {
         wraperwidth = "col-md-" + i;
         }
         }
         if(dropside="arriba")
         clon = $clon.insertBefore(dropelement).addClass("wrapping element");
         else
         clon = $clon.insertAfter(dropelement).addClass("wrapping element");

         dropelement.addClass("wrapping element");
         removeColClass(dropelement);

         $(".wrapping").wrapAll("<div class='wrapelem " + wraperwidth + "'></div>");

         dropelement.removeClass("wrapping");
         clon.removeClass("wrapping");

         }

         } */

        if (dropside == "izquierda" || dropside == "derecha") {
            if (numelements < 12) {
                $.each(dropelement.parent().children(), function () {
                    removeColClass($(this));
                })

                removeColClass(draged);

                if (draged.hasClass("element")) {
                    row = draged.parent();
                    draged.remove();
                    if (row.children(".element").length == 0)
                        row.remove();
                }
            }


            removeColClass($clon);
            if (numelements < 12) {
                if (draged.hasClass("element")) {
                    row = draged.parent();
                    draged.remove();
                    if (row.children(".element").length == 0)
                        row.remove();
                }
            }
            elements = items.toArray();
            rowsize = getRowSize(dropelement.parent());
            //console.log(rowsize);


            /*
             if (rowsize > 10) {
             for (i = 0; i < numelements; i++) {
             colsize = getColSize($(elements[i]));
             rowsize = getRowSize(dropelement.parent());
             if (colsize > 1)  {
             if(rowsize>10) if (numelements == 1) colsize = colsize - 1;  else colsize = colsize - 1;

             removeColClass($(elements[i]));
             $(elements[i]).addClass("col-md-" + colsize);
             }
             }

             if (numelements == 11) {
             if (dropside == "derecha") {
             $clon.insertAfter(dropelement).addClass("element col-md-1");
             }
             else
             $clon.insertBefore(dropelement).addClass("element col-md-1");
             }
             else {
             if (dropside == "derecha")
             $clon.insertAfter(dropelement).addClass("element col-md-2");
             else
             $clon.insertBefore(dropelement).addClass("element col-md-2");
             }
             }

             if(rowsize<=10){

             if (dropside == "derecha")
             $clon.insertAfter(dropelement).addClass("element col-md-2");
             else
             $clon.insertBefore(dropelement).addClass("element col-md-2");
             }

             */

            if (numelements == 1) {
                dropelement.addClass("col-md-6");
                coldraged = 6;

            }
            if (numelements == 2) {
                coldraged = 4;
                items.addClass(" element col-md-4");

            }

            if (numelements == 3) {
                coldraged = 3;
                items.addClass(" element col-md-3");
            }

            if (numelements == 4) {
                coldraged = 4;
                items.addClass(" element col-md-2");
            }
            if (numelements == 5) {
                coldraged = 2;
                items.addClass(" element col-md-2");
            }

            if (numelements == 6) {
                coldraged = 6;
                items.addClass(" element col-md-1");
            }

            if (numelements == 7) {
                coldraged = 5;
                items.addClass(" element col-md-1");
            }

            if (numelements == 8) {
                coldraged = 4;
                items.addClass(" element col-md-1");
            }

            if (numelements == 9) {
                coldraged = 3;
                items.addClass(" element col-md-1");
            }

            if (numelements == 10) {
                coldraged = 2;
                items.addClass(" element col-md-1");

            }
            if (numelements == 11) {
                coldraged = 1;
                items.addClass(" element col-md-1");

            }

            if (numelements >= 1 && numelements <= 11) {
                if (dropside == "izquierda")
                    $clon.insertBefore(dropelement).addClass("element col-md-" + coldraged);
                else
                    $clon.insertAfter(dropelement).addClass("element col-md-" + coldraged);

            }

            $("#boxHelper").remove();
            boxHelper = false;

        }

        $clon.children(".componentToolbar").addClass("compToolshow");


    }

    //Inicia el motor de drag para objetos del slidebar
    var init_drag_core = function () {
        $(".sidebar-nav .box").draggable({
            helper: "clone",
            handle: ".drag, .preview",
            drag: function (e, t) {
                dragging = true;
                t.helper.css('z-index', '9999');
                t.helper.css('background-color', 'black');
                t.helper.css('position', 'absolute');
                t.helper.find('.view').remove();
                if (t.helper.hasClass('widget_column')) {
                    //t.helper.removeClass('box box-element');
                    t.helper.addClass('appt_container');
                }
                light_drag($(this), e);


            },
            stop: function (e, ui) {
                dragging = false;
                if (dropelement) {
                    map_drop($(this));
                    $("#boxHelper").remove();
                    boxHelper = false;
                    originalsize = "";
                    reducedelement = "";

                    $("#black_layer_header,#black_layer_content,#black_layer_footer").children("span").removeClass("overlay_section");

                }

                init_drop();
                init_resize();
                init_drag_core();
                init_drag_elem();
                init_drop_boxes();

            }
        });

    }

    //Sobreescribo para elementos que pertenecen a la zona de trabajo
    var init_drag_elem = function () {
        $(".element").draggable({
            cursorAt: {left: 32, top: 25}, handle: ".drag",
            helper: function (e, ui) {
                var widget = $(this).attr("data-nombre");
                var ico = $("#palette ul [data-nombre=" + widget + "] i").attr("class");
                var boton = $('<div style="height: 50px;opacity:1;z-index:999999;cursor:move;width: 63px;padding-top:19px;padding-left:17px;background-color: black"><i style="font-size: 30px;color: #B2B2B2" class="' + ico + '"></i></div>');
                boton.css({position: "absolute"});
                return boton;
            },
            drag: function (e, ui) {
                dragging = true;

                light_drag($(this), e);
                $(".ui-resizable-handle").hide();
                $(".bar-handle").hide();
            },
            start: function (e, ui) {
                $(this).hide();

            },
            stop: function (e, ui) {
                dragging = false;
                $("#black_layer_header,#black_layer_content,#black_layer_footer").children("span").removeClass("overlay_section");
                $(this).show();


                if (dropelement) {
                    map_drop($(this));
                    $("#boxHelper").remove();
                    boxHelper = false;
                    originalsize = "";
                    reducedelement = "";
                }

                init_drop();
                init_resize();
                init_drag_core();
                init_drag_elem();
                init_drop_boxes();


            }
        });
    }

    //Se hacen los objetos dropeables
    var init_drop = function () {
        $(".row, #page_container, #header_container, #footer_container").droppable({
            accept: "[data-nombre='animacion'], [data-nombre='borde'], [data-nombre='color']",
            greedy: true,
            hoverClass: "drophere",
            // tolerance: "touch",
            drop: function (event, ui) {
                drop_extra(ui, $(this));

            },
            start: function (event, ui) {
                dropside = "";
            },
            over: function (event, ui) {
            },
            out: function (event, ui) {
                $(this).removeClass("drophere");

            }

        });


        $(".element").droppable({
            accept: ".box, .element,[data-nombre='animacion'], [data-nombre='borde'], [data-nombre='color']",
            hoverClass: "drophere",
            // tolerance: "touch",
            drop: function (event, ui) {
                //drop_extra(ui.helper,$(event.target));
                drop_extra(ui, $(this));

            },
            over: function (event, ui) {
                if (ui.helper.attr('data-nombre') != 'borde' && ui.helper.attr('data-nombre') != 'animacion' && ui.helper.attr('data-nombre') != 'color') {
                    $(this).removeClass("drophere");
                }

                ui.draggable.data("current-droppable", $(event.target));
            },
            out: function (event, ui) {
                $(this).removeClass("drophere");

            }

        });
    }

    //Se hacen los objetos resizables
    var init_resize = function () {
        $(".view").resizable();
        $(".view").resizable("destroy");

        $(".spaciator").resizable({
            handles: 's,e',
            start: function (e, ui) {
                resizing = true;

            },
            create: function (e, ui) {
                $(e.target).find(".ui-resizable-e").css("right", "-8px");

            },
            resize: function (e, ui) {

                if (e.shiftKey && ui.element.parent().next().length > 0) {
                    resizeRules2(ui);
                    item.css('height', '');
                } else {
                    resizeRules(ui);
                }

                item.css('width', '');

            }

        });


        $(".view").resizable({
            handles: 'e',
            create: function (e, ui) {
                if ($(e.target).find(".bar-handle").length == 0) {
                    $(e.target).append("<div class='bar-handle'></div>");
                }

            },
            start: function (e, ui) {
                resizing = true;
                ui.element.parent().find(".compToolshow").hide();
                lg(ui.element.parent());
                ui.element.parent().css("box-shadow","0 0 0 1px blue");
                ui.element.find(".bar-handle").addClass("show");
                ui.element.find(".ui-resizable-handle").addClass("show");

            },
            resize: function (e, ui) {
                if (e.shiftKey && ui.element.parent().next().length > 0) {
                    resizeRules2(ui);
                    item.css('height', '');

                } else {
                    resizeRules(ui);
                }
                item.css('width', '');
            },
            stop: function (e, ui) {
                resizing = false;
                ui.element.parent().css("box-shadow","");
                ui.element.parent().find(".compToolshow").show();
                item.css('width', '');
                ui.element.find(".bar-handle").removeClass("show");
                ui.element.find(".ui-resizable-handle").removeClass("show");
            }
        });


        $(".ui-resizable-handle").parent().parent().hover(
            function () {
                $(this).find(".ui-resizable-handle").show();
                $(this).find(".bar-handle").show();
            }, function () {
                $(this).find(".ui-resizable-handle").hide();
                $(this).find(".bar-handle").hide();

            }
        );

        $(".ui-resizable-handle").css("display", "none");

    }

    function randomNumber() {
        return randomFromInterval(1, 1e6);
    }

    function randomFromInterval(e, t) {
        return Math.floor(Math.random() * (t - e + 1) + e);
    }

    function agregarItemCarrusel(path) {
        return $('div').addClass('item').append($('img').attr('src', path));

    }

    function limpiarCheckGaleria(elemento) {
        $.each(elemento.find('#galeria input[type="checkbox"]'), function () {
            $(this).attr('checked', false);
        });

    }

    function getImagenesCarrusel(elementoCarrusel) {
        imagenes = Array();
        $.each(elementoCarrusel.find('div.item img'), function () {
            imagenes.push($(this).attr('src'));
        });

        return imagenes;
    }

    function cargarImagenesCarrusel(imgs) {
        $('input[name="imagenes_carrusel[]"]').each(function () {

            if ($.inArray($(this).val(), imgs) !== -1)
                $(this).attr('checked', true);
        });
    }

    function LimpiarHtmlPublicar() {
        cloneContainer = $('#fullpage').clone();
        cloneContainer.find('div.elementoCuadranteItem').remove();
        cloneContainer.find('.remove_feature').remove();
        cloneContainer.find('.clone_feature').remove();
        cloneContainer.find('.widget_demo').remove();
        cloneContainer.removeClass('box box-element ui-draggable');
        $.each(cloneContainer.find('.editable'), function () {
            $(this).removeClass('editable cke_editable cke_editable_inline cke_contents_ltr');
        });

        $.each(cloneContainer.find('h1, h2, h3, h4, p, address'), function () {
            $(this).removeAttr('contenteditable tabindex spellcheck role aria-label title aria-describedby ');
            $(this).removeClass('cke_editable cke_editable_inline cke_contents_ltr');
        });

        return cloneContainer;
    }

    appView = Backbone.View.extend({
        initialize: function () {
            this.render();
            $(this.el).unbind();
        },
        render: function () {
        },
        events: {
            'click  #save_page': 'savePage',
            "click .color_elem": "color_elem",
            'click  #publish_page': 'publishPage',
            'click  .set_color': 'setColor',
            "click  #grid": "grid",
            "click  #boxed": "boxed",
            "click  #fullwidth": "fullwidth",
            "click .img_bg": "galBg",
            "click .img_pat": "galPat",
            "click .rmv_bgBody": "removeBgBody",
            "click .rmv_bgContainer": "removeBgContainer",
            //hide menus
            "click #fullpage, #sidebar-nav": "hideMenus",
            //boxed actions
            "click #boxed-bg-body img": "changeBgBody",
            "click #boxed-patterns-body img": "changePatBody",
            "click #boxed-bg-container img": "changeBgContainer",
            "click #boxed-patterns-container img": "changePatContainer",
            "click #full-patterns-container img": "changePatFull",
            //removeimages_boxed
            "click .imagebox .removebg ": "removeimg",
            //remove color
            "click .rmv_colBody": "removecolBody",
            "click .rmv_colContainer": "removecolContainer",
            "click .rmv_fullpat": "removeFullPat",
            "click .font-tit p": "changeFontTitles",
            "click .font-cont p": "changeFontContent"


        },
        setAnimacion: function (e) {
            alert("animar");

        },
        color_elem: function (event) {
            color = $("#colorText").data("color");
            color_element.css("background-color", color);
        },
        grid: function () {
            $(".sw-grid").toggleClass("show");
            altura = $("#fullpage").height() + 2000;
            $(".sw-grid").children(".container").height(altura);
        },
        boxed: function (event) {
            event.stopPropagation();

            img = $("#boxedbg").attr("data-bgimg");


            $("#fullpage").css("background", img);
            $("#fullpage").css("background-size", "100% 100%");
            $("#fullpage").css("width", "80%");
            $("#fullpage").css("max-width", "1170px");
            $("#fullpage").css("margin-left", "auto");
            $("#fullpage").css("margin-right", "auto");


            if ($("#side-opt-boxed").hasClass("active")) {
                $("#boxed div").removeClass("pressedBoxed");
                $("#side-opt-boxed").animate({
                    right: "-=182",
                    opacity: "hide"
                }, 300, function () {
                });
                $("#side-opt-boxed").removeClass("active");

            }
            else {
                menuHider();
                $("#fullwidth div").removeClass("pressedFull");
                $("#boxed div").addClass("pressedBoxed");

                $("#side-opt-boxed").addClass("active");
                $("#side-opt-boxed").animate({
                    right: "+=182",
                    opacity: "show"
                }, 300, function () {
                });
            }

        },
        fullwidth: function (event) {
            event.stopPropagation();
            $("#fullpage").css("width", "100%");
            $("#fullpage").css("max-width", "");

            img = $("#fullpat").attr("data-bgimg");

            $("#fullpage").css("background", img);


            if ($("#side-opt-fullwidth").hasClass("active")) {
                $("#fullwidth div").removeClass("pressedFull");

                $("#side-opt-fullwidth").animate({
                    right: "-=182",
                    opacity: "hide"
                }, 300, function () {
                });
                $("#side-opt-fullwidth").removeClass("active");

            }

            else {
                menuHider();

                $("#boxed div").removeClass("pressedBoxed");
                $("#fullwidth div").addClass("pressedFull");

                $("#side-opt-fullwidth").addClass("active");
                $("#side-opt-fullwidth").animate({
                    right: "+=182",
                    opacity: "show"
                }, 300, function () {
                });
            }

        },
        galPat: function (e) {
            image_selector = new ImageSelectorView({el: $('#upload_modal'), image_type: "pattern"});

        },
        galBg: function (e) {
            image_selector = new ImageSelectorView({el: $('#upload_modal'), image_type: "background"});
        },
        changeBgBody: function (e) {
            img = $(e.target).attr("original");
            color = $("body").css("background-color");
            $("body").css("background", "url('" + img + "') 50% 50%  no-repeat fixed");
            $("body").css("background-size", "100% 100%");
            $("body").css("background-color", color);

        },
        changePatBody: function (e) {
            img = $(e.target).attr("original");
            color = $("body").css("background-color");
            $("body").css("background", "url('" + img + "') 0% 0% repeat fixed");
            $("body").css("background-color", color);
        },
        changeBgContainer: function (e) {
            img = $(e.target).attr("original");
            color = $("#fullpage").css("background-color");
            $("#fullpage").css("background", "url('" + img + "') 50% 50%  no-repeat fixed");
            $("#fullpage").css("background-size", "100% 100%");
            $("#fullpage").css("background-color", color);

            $("#boxedbg").attr("data-bgimg", "url('" + img + "') 50% 50%  no-repeat fixed " + color);
        },
        changePatContainer: function (e) {
            img = $(e.target).attr("original");
            color = $("#fullpage").css("background-color");
            $("#fullpage").css("background", "url('" + img + "') 0% 0% repeat fixed");
            $("#fullpage").css("background-color", color);

            $("#boxedbg").attr("data-bgimg", "url('" + img + "') 0% 0% repeat fixed " + color);
        },

        changePatFull: function (e) {
            img = $(e.target).attr("original");
            color = $("#fullpage").css("background-color");
            $("#fullpage").css("background", "url('" + img + "') 0% 0% repeat fixed");
            $("#fullpage").css("background-color", color);

            $("#fullpat").attr("data-bgimg", "url('" + img + "')  0% 0% repeat fixed " + color);
        },

        removeimg: function (e) {
            srcimg = $(e.target).parent().children("img").attr("src");

            var bg_body = $("body").css('background-image');
            bg_body = bg_body.replace('url(', '').replace(')', '');
            var x = bg_body;
            x = bg_body.split("/");
            url_body = "/" + x[3] + "/" + x[4] + "/" + x[5] + "/" + x[6];

            var bg_body = $("#fullpage").css('background-image');
            bg_body = bg_body.replace('url(', '').replace(')', '');
            var x = bg_body;
            x = bg_body.split("/");
            url_container = "/" + x[3] + "/" + x[4] + "/" + x[5] + "/" + x[6];

            if (confirm("¿Desea borrar esta imágen?")) {
                image_id = $(e.target).parent().attr("id");
                id = image_id.split("_");
                id = id[1];
                $.ajax({
                    url: "/app_dev.php/build_galleryimage/" + id,
                    type: "DELETE",
                    success: function () {
                        if (srcimg == url_body)
                            $("body").css('background-image', "");

                        if (srcimg == url_container)
                            $("#fullpage").css('background-image', "");

                        $("#image_" + id).remove();

                    }
                });

            }
        },
        removeBgBody: function (e) {
            $('body').css("background-image", "none");
        },
        removeBgContainer: function (e) {
            $('#fullpage').css("background-image", "none");
            $("#boxedbg").attr("data-bgimg", "");
        },
        removecolBody: function (e) {
            $('body').css("background-color", "");
        },
        removecolContainer: function (e) {
            $('#fullpage').css("background-color", "");
            $("#boxedbg").attr("data-bgimg", $('#fullpage').css("background"));
        },
        removeFullPat: function (e) {
            $('#fullpage').css("background-image", "none");
            $("#fullpat").attr("data-bgimg", "");
        },
        hideMenus: function (e) {
            menuHider();

        },
        savePage: function (e) {
            e.preventDefault();
            $("#calendar").html("");
            contenidoTwitter();
            // copiarGallery();
            contenidoPinterest();
            contenidoLinkedin();
            json_asset = JSON.stringify(page.get('assets').toJSON());
            page_view.save();

            edit_content = $("#page_container").html();
            css_content = $("#page_container").attr("style");
            class_content = $("#page_container").attr("class");

            edit_footer = $("#footer_container").html();
            css_footer = $("#footer_container").attr("style");
            class_footer = $("#footer_container").attr("class");

            edit_header = $("#header_container").html();
            css_header = $("#header_container").attr("style");
            class_header = $("#header_container").attr("class");

            $("#save_page").html('<i class="fa fa-refresh"></i> ... ');
            //css = $("#page_container").attr("style");
            css = $("body").attr("style");

            //Obtener reglas de google Fonts

            fontContent = $('#stylecontent').val();
            fontTitles = $('#styletitles').val();


            cssContainer = $("#fullpage").attr("style");
            menuthemefinal = $("#apptibasemenu").attr("menutheme");

            console.info(menuthemefinal);
            if ($("#fullpage").css("background") != "")


                $(document).ajaxStop($.unblockUI);
            $.blockUI({
                css: {
                    border: 'none',
                    padding: '0px',
                    width: '0px',
                    left: '50%',
                    backgroundColor: '#000',
                    '-webkit-border-radius': '10px',
                    '-moz-border-radius': '10px',
                    opacity: 1,
                    color: '#fff'
                },
                message: '<i style="color:#ffffff;font-size: 80px" class="fa fa-spin fa-spinner"/>'
            });

            $.ajax({
                url: page.get('url_save'),
                method: "POST",
                data: {
                    assets: json_asset,
                    edit_content: edit_content,
                    edit_footer: edit_footer,
                    edit_header: edit_header,
                    css: css,
                    cssContainer: cssContainer,
                    class_footer: class_footer,
                    css_footer: css_footer,
                    class_header: class_header,
                    css_header: css_header,
                    class_content: class_content,
                    css_content: css_content,
                    menutheme: menuthemefinal,
                    fontTitles: fontTitles,
                    fontContent: fontContent
                },
                success: function () {
                    $("#save_page").html('<i class="fa fa-save"></i> Guardar&nbsp; ');
                }
            });


        }, publishPage: function () {
            assets = {};
            page_view.publish();
//        //aqui la limpieza de la pagina
            $.each($("#fullpage").find(".widget_render"), function (k, v) {
                url = $(v).attr('url');
                asset = $(v).attr('asset');
                assets[asset] = asset;

                if (url !== "#") {
                    $.ajax({
                        url: url,
                        data: {json_data: $(v).html()},
                        dataType: "json",
                        success: function (result) {
                            $(v).html(result.url);
                            $(v).attr("url", "#");
                        }
                    });
                }

            });
            $(".sw-grid").removeClass("show");

            html = LimpiarHtmlPublicar();
            lg(html);

            html_container = html.children().children("#page_container").html();
            html_header = html.children().children("#header_container").html();
            html_footer = html.children().children("#footer_container").html();


            css_footer = html.children().children("#footer_container").attr("style");
            class_footer = html.children().children("#footer_container").attr("class");

            css_header = html.children().children("#header_container").attr("style");
            class_header = html.children().children("#header_container").attr("class");

            css_content = html.children().children("#page_container").attr("style");
            class_content = html.children().children("#page_container").attr("class");


            $('body').toggleClass('no-image devpreview sourcepreview');
            $('body').css('margin-right', '0px !important');
            $('#sidebar-nav').css('display', 'none');
            var background = $('body').css('background');
            $("#app_container").css('background', background);

            // unblock when ajax activity stops
            $(document).ajaxStop($.unblockUI);
            $.blockUI({
                css: {
                    border: 'none',
                    padding: '0px',
                    width: '0px',
                    left: '50%',
                    backgroundColor: '#000',
                    '-webkit-border-radius': '10px',
                    '-moz-border-radius': '10px',
                    opacity: 1,
                    color: '#fff'
                },
                message: '<i style="color:#ffffff;font-size: 80px" class="fa fa-spin fa-spinner"/>'
            });


            setTimeout(function () {
                screenshoot($("#app_container"), function (image64) {
                    $.ajax({
                        url: page.get('url_publish'),
                        method: "POST",
                        data: {
                            id: 1,
                            assets: assets,
                            html_container: html_container,
                            html_header: html_header,
                            html_footer: html_footer,
                            class_footer: class_footer,
                            css_footer: css_footer,
                            class_header: class_header,
                            css_header: css_header,
                            class_content: class_content,
                            css_content: css_content,
                            image64: image64
                        },
                        success: function (data) {
                            $('body').toggleClass('no-image devpreview sourcepreview');
                            $('body').css('margin-right', '');
                            $('#sidebar-nav').css('display', 'inherit');
                            $("#app_container").css('background', 'inherit');
                        }
                    });
                });
            }, 10);
        }, setColor: function (e) {
            new_color = $("#color").val();
//            if (current_div !== null){
//                current_div.css("background", new_color);
//            }else{
            //$("#page_container").css("background", new_color);
            $("body").css("background-color", new_color);
//            }
        },
        changeFontTitles: function (e) {
            font = $(e.target).attr("data-font");
            $(e.target).parent().children().removeClass("fontactive");
            $(e.target).addClass("fontactive");
            var style = $('<style id="textcontent">#fullpage h1,#fullpage  h2,#fullpage  h3,#fullpage  h4{ font-family:' + font + '}</style>');
            $('html > head').append(style);
            $("#styletitles").val(font);

        },
        changeFontContent: function (e) {
            font = $(e.target).attr("data-font");
            $(e.target).parent().children().removeClass("fontactive");
            $(e.target).addClass("fontactive");
            var style = $('<style id="textcontent">#fullpage p,#fullpage  li,#fullpage  ul{ font-family:' + font + '}</style>');
            $('html > head').append(style);
            $("#stylecontent").val(font);

        }

    });

    ImageSelectorView = Backbone.View.extend({
        initialize: function () {
            image_type = this.options.image_type;
            $("#images_type").val(image_type);
            $(this.el).modal();
            if (image_type != "background" && image_type != "pattern") {
                $(".selectimages").show();
                $(".selectimages").tab('show');
                $("#galeria").html('<i style="color:#000000;font-size: 35px" class="fa fa-spin fa-spinner"/>');
                $.ajax({
                    url: page.get('url_gallery'),
                    data: {galery_id: page.get('admin'), image_type: image_type},
                    type: 'GET',
                    success: function (data) {
                        $("#galeria").html(data);
                        $("#galeria").slimScroll({height: '380px'});
                        limpiarCheckGaleria($(this.el));
                        if (carrusel === undefined)
                            carrusel = false;
                        if (carrusel) {
                            padre = current_img.parent().parent();
                            cargarImagenesCarrusel(getImagenesCarrusel(padre));
                            $('input[name="imagenes_carrusel[]"]').show();
                            $(".help_carrusel").show();
                        } else {
                            $(".help_carrusel").hide();
                            $('input[name="imagenes_carrusel[]"]').hide();
                        }

                    },
                    error: function (jqXHR, textStatus, error) {
                        alert("error: " + jqXHR.responseText);
                    }
                });

            }
            else {
                $(".selectimages").hide();
                $(".uploadimages").tab('show');
            }
            $(this.el).unbind();
        },
        events: {
            'click .close': 'closeModal',
            'click .delete': 'deleteImage',
            'click .img_select': 'selectImage',
            "click #actualizar_imagenes": "actualizarImagenes",
            "change #search_images": "buscarImagenes",
        },
        closeModal: function () {
            this.options.background = false;
        },
        selectImage: function (evt) {
            if (this.options.background) {
                url = $(evt.target).attr('original');
            }

            else {
                $(current_img).attr('src', ($(evt.target).attr('original')));
            }
        },
        deleteImage: function (evt) {
            url = $(evt.target).attr('target');
            $.ajax({
                url: url,
                method: 'DELETE',
                success: function (result) {
                    $("#image_" + result).hide(300);
                    return true;
                }
            });
            //console.info(target);
        },
        actualizarImagenes: function () {
            if (carrusel) {
                elementoCarrusel = current_img.parent().parent();
                num_selected = $('input[name="imagenes_carrusel[]"]:checked').length;
                if (num_selected > 0) {
                    elementoCarrusel.empty();
                    loop = 1;

                    $('input[name="imagenes_carrusel[]"]:checked').each(function () {
                        activo = loop === 1 ? 'active' : '';
                        $(elementoCarrusel).append($('<div class="item ' + activo + '"><img alt="" src="' + $(this).val() + '"> </div>'));
                        loop++;
                    });
                }

//                $(current_img).jWindowCrop({
//                    targetWidth: 600,
//                    targetHeight: 300,
//                    loadingText: 'hello world',
//                    onChange: function(result) {
//                        $('#crop_x').text(result.cropX);
//                        $('#crop_y').text(result.cropY);
//                        $('#crop_w').text(result.cropW);
//                        $('#crop_h').text(result.cropH);
//                    }
//                });

            } else {
//                $(current_img).dragncrop();
//                $(current_img).Jcrop({
//                    onChange: function(){
//                        console.info("adasd");
//                    }
//                });
            }

            $(this.el).modal('hide');
        },
        buscarImagenes: function (evt) {
            url = $(evt.target).val();
            $("#galeria").load(url, function () {
                $("#galeria").slimScroll();
            });
        }
    });

    PageView = Backbone.View.extend({
        events: {
            "click .editable": "ck_editable",
            "click .remove": "remove",
            "click .remove-1": "remove1",
            "click .edit": "edit",
            "click .ajax": "render",
            "dblclick img": "select_image", //separar de aqui
            "click #render_calendar": "renderCalendar", //separar de aqui
            "click #render_poll": "renderPoll", //separar de aqui
            "dblclick a.linkeable": "configLink", //todos los links de clase editable
            "dblclick a[contenteditable=true]": "configLink", //todos los links de clase editable
            "dblclick .dinamic_menu a": "stopDefault", //todos los links de clase editable
            "click .apptibasemenu": "setThemeMenu",
            "click .new_tab": "addTab",
            "click .new_accordion": "addAccordion",
            "mouseenter .element": "elementHovered",
            "mouseleave .element": "elementUnhovered",
            "click .column": "getDiv",
            "click #render_menu": "renderMenu", //separar de aqui
            "click .espaciador": "espaciador",
            "click #render_facebook": "renderFacebook",
            "click #render_twitter": "renderTwitter",
            "click #render_google_plus": "renderGooglePlus",
            "click #render_instagram": "renderInstagram",
            "click #render_linkedin": "renderLinkedin",
            "click #render_pinterest": "renderPinterest",
            "click #render_youtube": "renderYoutube",
            "click #render_vimeo": "renderVimeo",
            "click #render_share_bar": "renderSocialBar",
            "click .remove_accordion": "removeAccordion",
            "click .new_link": "newLink",
            "click .clone_feature": "cloneFeature",
            "click .remove_feature": "removeFeature",
            //"click #render_facebook": "renderFacebook",
        },
        initialize: function () {

            init_rowcontol();
            init_drag_core();
            init_drag_elem();
            init_drop();
            init_resize();
            init_drop_boxes();
            $(this.el).unbind();


            image = $("#fullpage").css("background-image");

            if ($("#fullpage").css('max-width') == "none") {
                $("#fullpat").attr("data-bgimg", image);
            }
            else {
                $("#boxedbg").attr("data-bgimg", image);
            }


        },
        newLink: function (event) {
            $(event.target).parent().parent().before('<li><a href="#" class="linkeable">Link <i class="fa fa-trash-o remove-1"></i></a></li>');
        },
        configLink: function (event) {
            event.preventDefault();
            link_elem = $(event.target);
            current_link = link_elem;
            link_view = new LinkView({el: $("#link_modal")});
        },
        stopDefault: function (event) {
            event.preventDefault();
        },
        addTab: function (e) {
            elem = $(e.target);
            id = randomNumber();
            elem.before('<li><a href="#tab' + id + '" data-toggle="tab" contenteditable="true">New Tab</a></li>');
            elem.parent().parent().parent().find(".tab-content").append('<div class="tab-pane" id="tab' + id + '"><div class="col-md-12 column"> <div class="drop_init drop_content ui-droppable"></div> </div> <a class="remove_tab builder_tool"> Eliminar tab </a> </div>');
            elem.parent().parent().parent().find(".column").sortable({connectWith: ".column", helper: "clone"});
        },
        setThemeMenu: function () {
            $('#tabbar a[href=#pageinspector]').tab('show');
        },
        addAccordion: function (e) {

            elem = $(e.target);
            id = randomNumber();
            elem.before('<div class="panel panel-default"><div class="panel-heading"><a class="panel-title" data-toggle="collapse" data-parent="#myAccordion" href="#' + id + '" contenteditable="true">Nuevo Item <a class="btn btn-danger remove-1"><i class="fa fa-trash-o"></i></a> </a></div><div id="' + id + '" class="panel-collapse collapse"><div class="col-md-12 column"><div class="drop_init drop_content ui-droppable"></div></div></div></div>');
            elem.parent().find(".column").sortable({connectWith: ".column", helper: "clone"});

        },
        removeAccordion: function (e) {
            elem = $(e.target);
            tab = elem.parent().parent().parent();
        },
        renderMap: function () {
            mapview = new MapView({el: $("#map_modal")});
        },
        renderPoll: function () {
            try {
                mainpoll.render(); //para que cree una sola instancia
            } catch (e) {
                mainpoll = new mainView({el: $("#poll_modal")});
            }
        },
        renderCalendar: function () {
            try {
                calendar.render(); //para que cree una sola instancia
            } catch (e) {
                calendar = new CalendarView({el: $("#calendar")});
            }
        },
        renderFacebook: function () {
            try {
                facebook.render(); //para que cree una sola instancia
            } catch (e) {
                facebook = new FacebookView({el: $("#face_modal")});
            }
        },
        renderTwitter: function () {
            try {
                twitter.render();
            } catch (e) {
                twitter = new TwitterView({el: $("#twitter_modal")})
            }
        },
        renderGooglePlus: function () {
            try {
                google_plus.render();
            } catch (e) {
                google_plus = new GooglePlusView({el: $("#google_modal")})
            }
        },
        renderInstagram: function () {
            try {
                instagram.render();
            } catch (e) {
                instagram = new InstagramView({el: $("#instagram_modal")})
            }
        },
        renderLinkedin: function () {
            try {
                linkedin.render();
            } catch (e) {
                linkedin = new LinkedinView({el: $("#linkedin_modal")})
            }
        },
        renderPinterest: function () {
            try {
                pinterest.render();
            } catch (e) {
                pinterest = new PinterestView({el: $("#pinterest_modal")})
            }
        },
        renderYoutube: function () {
            try {
                youtube.render();
            } catch (e) {
                youtube = new YouTubeView({el: $("#youtube_modal")})
            }
        },
        renderVimeo: function () {
            try {
                vimeo.render();
            } catch (e) {
                vimeo = new VimeoView({el: $("#vimeo_modal")})
            }
        },
        renderSocialBar: function () {
            try {
                socialBar.render();
            } catch (e) {
                socialBar = new SocialShareView({el: $("#social_share_modal")})
            }
        },
        espaciador: function () {
            espaciador = new EspaciadorView({el: $("#espaciador_modal")})
        },
        select_image: function (evt) {
            current_img = $(evt.target);
            carrusel = current_img.parent().hasClass('item') ? true : false;
            image_selector = new ImageSelectorView({el: $('#upload_modal'), image_type: "content"});
        },
        ck_editable: function (event) {
            id = $(event.currentTarget).attr('id');
            try {
                if (id === undefined) {
                    id = "elem" + randomNumber();
                    $(event.currentTarget).attr('id', id);
                    $(event.currentTarget).attr('contenteditable', true);
                    CKEDITOR.inline(id, {startupFocus: true});

                }
                if (CKEDITOR.instances[id] === undefined) {
                    CKEDITOR.inline(id, {startupFocus: true});
                }
            } catch (e) {
                //ignore
            }
        },
        elementHovered: function(event){
            //lg("entra a elemento");



        },
        elementUnhovered: function(event){
            //lg("sale de elemento");


        },
        remove: function (event) {
            elem = $(event.currentTarget).parent().parent();
            parent = $(elem).parent();

            elem.remove();

            if (parent.children().length == 0)
                parent.remove();

            if (($("#page_container").children().length == 0) || ($("#header_container").children().length == 0) || ($("#footer_container").children().length == 0)) {
                init_drop_boxes();
            }

            /*   items = parent.children();
             size = items.size();
             md = 12 / size;
             for(i=1; i<12; i++){
             items.removeClass("col-md-" + i);
             }
             items.addClass("col-md-" + md);
             items.transition({ scale: 0.9 });
             items.transition({ scale: 1 });  */

        },
        remove1: function (event) {
            $(event.target).parent().parent().remove();
        },
        edit: function (event) {
            div = $(event.target).parent().parent().parent().parent().find(".view");
            if (!div.hasClass("selected")) {
                $(".view").removeClass("selected");
                div.addClass("selected");
            }
            else
                div.removeClass("selected");
        },
        save: function () {
            this.model.set('edit_content', this.el.innerHTML);
        },
        publish: function () {
            html_limpio = "<h1>CLEAN HTML</h1>";
            this.model.set('html', html_limpio);
        },
        render: function (evt) {
            evt.preventDefault();
            url = $(evt.target).attr('href');
            $(evt.target).parent().load(url);
        },
        getDiv: function (e) {
            //@todo: revisar esta parte:

            current_div = $(e.target);
            $(".column").removeClass("current_div");
            if (current_div.hasClass("column")) {
                current_div.addClass("current_div");
            }
            $("#color").val(current_div.css("backgroundColor"));
        },
        renderMenu: function () {
            try {
                menu.render(); //para que cree una sola instancia
            } catch (e) {
                menu = new MenuView({el: $("#menu_modal")});
            }
        },
        removeFeature: function (e) {
            $(e.target).parent().remove();
        },
        cloneFeature: function (e) {
            elem = $(e.target).parent();
            elem.after(elem.clone());
        }
});

    /* Model para el tutorial del web builder */
    TutorialView = Backbone.View.extend({
        events: {
            "click #tutorial": "abrirTutorial"
        },
        abrirTutorial: function () {
            introJs().start();
        }
    });

    LinkView = Backbone.View.extend({
        initialize: function () {
            this.render();
            $(this.el).unbind();
        },
        render: function () {
            $(this.el).modal();

        },
        events: {
            "click #save_link": "saveLink"
        },
        saveLink: function (e) {

            url = $("#a_link").val();
            title = $("#a_title").val();

            $(link_elem).attr("href", url);
            $(link_elem).html(title + ' <i class="fa fa-trash-o remove-1">');
        }
    });

});
