define(["jquery", "jqueryclean", "jquery_ui", "intro", "transition"], function($, jqueryclean, jquery_ui, introJs, transition) {
    var flag = false;

    function randomNumber() {
        return randomFromInterval(1, 1e6)
    }
    function randomFromInterval(e, t) {
        return Math.floor(Math.random() * (t - e + 1) + e)
    }


    function gridSystemGenerator() {
        $(".appt_container .preview input").bind("keyup", function() {
            var e = 0;
            var t = "";
            var n = false;
            var r = $(this).val().split(" ", 12);
            $.each(r, function(r, i) {
                if (!n) {
                    if (parseInt(i) <= 0)
                        n = true;
                    e = e + parseInt(i);
                    t += '<div class="col-md-' + i + ' column"></div>'
                }
            });
            if (e == 12 && !n) {
                $(this).parent().next().children().html(t);
                $(this).parent().prev().show()
            } else {
                $(this).parent().prev().hide()
            }
        })
    }
    function configurationElm(e, t) {
        $(".demo").delegate(".configuration > a", "click", function(e) {
            e.preventDefault();
            var t = $(this).parent().next().next().children();
            $(this).toggleClass("active");
            t.toggleClass($(this).attr("rel"))
        });
        $(".demo").delegate(".configuration .dropdown-menu a", "click", function(e) {
            e.preventDefault();
            var t = $(this).parent().parent();
            var n = t.parent().parent().next().next().children();
            t.find("li").removeClass("active");
            $(this).parent().addClass("active");
            var r = "";
            t.find("a").each(function() {
                r += $(this).attr("rel") + " "
            });
            t.parent().removeClass("open");
            n.removeClass(r);
            n.addClass($(this).attr("rel"))
        });
    }
    function removeElm() {
        $(".demo").delegate(".remove", "click", function(e) {
            e.preventDefault();

            $(this).parent().parent().remove();


            if (!$(".demo .appt_container").length > 0) {
                clearDemo()
            }
        })

    }
    function clearDemo() {
        $(".demo").empty()
    }
    function removeMenuClasses() {
        $("#menu-layoutit li button").removeClass("active")
    }
    function changeStructure(e, t) {
        $("#download-layout ." + e).removeClass(e).addClass(t)
    }
    function cleanHtml(e) {
        $(e).parent().append($(e).children().html())
    }
    function downloadLayoutSrc() {
        var e = "";
        $("#download-layout").children().html($(".demo").html());
        var t = $("#download-layout").children();
        t.find(".preview, .configuration, .drag, .remove").remove();
        t.find(".lyrow").addClass("removeClean");
        t.find(".box-element").addClass("removeClean");
        t.find(".lyrow .lyrow .lyrow .lyrow .lyrow .removeClean").each(function() {
            cleanHtml(this)
        });
        t.find(".lyrow .lyrow .lyrow .lyrow .removeClean").each(function() {
            cleanHtml(this)
        });
        t.find(".lyrow .lyrow .lyrow .removeClean").each(function() {
            cleanHtml(this)
        });
        t.find(".lyrow .lyrow .removeClean").each(function() {
            cleanHtml(this)
        });
        t.find(".lyrow .removeClean").each(function() {
            cleanHtml(this)
        });
        t.find(".removeClean").each(function() {
            cleanHtml(this)
        });
        t.find(".removeClean").remove();
        $("#download-layout .column").removeClass("ui-sortable");
        $("#download-layout .row-fluid").removeClass("clearfix").children().removeClass("column");
        if ($("#download-layout .container").length > 0) {
            changeStructure("row-fluid", "row")
        }
        formatSrc = $.htmlClean($("#download-layout").html(), {format: true, allowedAttributes: [["id"], ["class"], ["data-toggle"], ["data-target"], ["data-parent"], ["role"], ["data-dismiss"], ["aria-labelledby"], ["aria-hidden"], ["data-slide-to"], ["data-slide"]]});
        $("#download-layout").html(formatSrc);
        $("#downloadModal textarea").empty();
        $("#downloadModal textarea").val(formatSrc)
    }

    var currentDocument = null;
    var timerSave = 2e3;


    $(window).resize(function() {
        $("body").css("min-height", $(window).height() - 90);
        $(".demo").css("min-height", $(window).height() - 160)
    });




    $(document).ready(function() {
        $("body").css("min-height", $(window).height() - 90);
        $(".demo").css("min-height", $(window).height() - 160);

//inicializando soporte para dispositivos touch
        (function($) {
            // Detect touch support
            $.support.touch = 'ontouchend' in document;
            // Ignore browsers without touch support
            if (!$.support.touch) {
                return;
            }
            var mouseProto = $.ui.mouse.prototype,
                _mouseInit = mouseProto._mouseInit,
                touchHandled;

            function simulateMouseEvent(event, simulatedType) { //use this function to simulate mouse event
                // Ignore multi-touch events
                if (event.originalEvent.touches.length > 1) {
                    return;
                }
                event.preventDefault(); //use this to prevent scrolling during ui use

                var touch = event.originalEvent.changedTouches[0],
                    simulatedEvent = document.createEvent('MouseEvents');
                // Initialize the simulated mouse event using the touch event's coordinates
                simulatedEvent.initMouseEvent(
                    simulatedType, // type
                    true, // bubbles
                    true, // cancelable
                    window, // view
                    1, // detail
                    touch.screenX, // screenX
                    touch.screenY, // screenY
                    touch.clientX, // clientX
                    touch.clientY, // clientY
                    false, // ctrlKey
                    false, // altKey
                    false, // shiftKey
                    false, // metaKey
                    0, // button
                    null              // relatedTarget
                );

                // Dispatch the simulated event to the target element
                event.target.dispatchEvent(simulatedEvent);
            }
            mouseProto._touchStart = function(event) {
                var self = this;
                // Ignore the event if another widget is already being handled
                if (touchHandled || !self._mouseCapture(event.originalEvent.changedTouches[0])) {
                    return;
                }
                // Set the flag to prevent other widgets from inheriting the touch event
                touchHandled = true;
                // Track movement to determine if interaction was a click
                self._touchMoved = false;
                // Simulate the mouseover event
                simulateMouseEvent(event, 'mouseover');
                // Simulate the mousemove event
                simulateMouseEvent(event, 'mousemove');
                // Simulate the mousedown event
                simulateMouseEvent(event, 'mousedown');
            };

            mouseProto._touchMove = function(event) {
                // Ignore event if not handled
                if (!touchHandled) {
                    return;
                }
                // Interaction was not a click
                this._touchMoved = true;
                // Simulate the mousemove event
                simulateMouseEvent(event, 'mousemove');
            };
            mouseProto._touchEnd = function(event) {
                // Ignore event if not handled
                if (!touchHandled) {
                    return;
                }
                // Simulate the mouseup event
                simulateMouseEvent(event, 'mouseup');
                // Simulate the mouseout event
                simulateMouseEvent(event, 'mouseout');
                // If the touch interaction did not move, it should trigger a click
                if (!this._touchMoved) {
                    // Simulate the click event
                    simulateMouseEvent(event, 'click');
                }
                // Unset the flag to allow other widgets to inherit the touch event
                touchHandled = false;
            };
            mouseProto._mouseInit = function() {
                var self = this;
                // Delegate the touch handlers to the widget's element
                self.element
                    .on('touchstart', $.proxy(self, '_touchStart'))
                    .on('touchmove', $.proxy(self, '_touchMove'))
                    .on('touchend', $.proxy(self, '_touchEnd'));

                // Call the original $.ui.mouse init method
                _mouseInit.call(self);
            };
        })(jQuery);


        $(".sidebar-nav .box ,.cuadrante .box").draggable({connectToSortable: ".column", helper: "clone", handle: ".drag, .preview", drag: function(e, t) {
            //t.helper.width(400);
            t.helper.css('z-index', '9999');
            t.helper.css('background-color', 'black');
            t.helper.css('position', 'absolute');
            t.helper.find('.view').remove();
            if (t.helper.hasClass('widget_column')) {
                //t.helper.removeClass('box box-element');
                t.helper.addClass('appt_container');
            }

        }, stop: function(e, t) {
            $(".box").droppable({accept: "[data-nombre='animacion'], [data-nombre='borde']", hoverClass: "ui-dropable-placeholder", greedy: true,
                drop: function(event, ui) {
                    if (ui.helper.attr('data-nombre') == 'borde') {
                        var borde = ui.helper.attr('data-borde');
                        $(this).attr('class', 'box box-element ui-draggable ui-droppable');
                        $(this).attr('class', borde + ' border_box box box-element ui-draggable ui-droppable');
                    }

                    else {
                        var animacion = ui.helper.attr('data-animacion');
                        //Reinicio los css
                        $(this).attr('class', 'box box-element ui-draggable ui-droppable');
                        $(this).attr('class', animacion + ' box box-element ui-draggable ui-droppable');
                        // $( this ).children("span:first").append("<a class='drag'><i class='fa fa-move'></i></a>");
                    }

                }
            });

            $(".view").resizable({
                handles: 'e',
                start: function(e,ui){
                    $(this).css({
                        position: "relative !important",
                        top: "0 !important",
                        left: "0 !important"
                    });
                },

                stop: function(e, ui) {
                    item = ui.element;
                    size_son = item.width();
                    size_parent = item.parent().parent().width();
                    ancho_columna = size_parent/12;
                    console.log(ancho_columna);

                    for (i = 1; i < 12; i++) {
                        item.removeClass("col-md-" + i);
                    }

                    if((size_son<ancho_columna) )
                        item.addClass("col-md-1");

                    else
                    if(size_son<4*ancho_columna)
                        item.addClass("col-md-4");

                    if(size_son<(6*ancho_columna))
                        item.addClass("col-md-8");

                    if(size_son<(8*ancho_columna))
                        item.addClass("col-md-8");

                    if(size_son<(12*ancho_columna))
                        item.addClass("col-md-12");


                    columna = size_parent/size_son;
                    columna = Math.round(columna);
                    console.log(columna);


                    item.css('width','');


                }
            });




            $( ".view" ).resizable( "enable" );

            $(".column").sortable({connectWith: ".column", cursorAt: {left: 20}, opacity: 0.8,
                helper: function(e, ui) {
                    var widget = $(ui.context.outerHTML).attr("data-nombre");
                    var ico = $("#palette ul [data-nombre=" + widget + "] i").attr("class");
                    var boton = $('<i style="font-size: 35px" class="' + ico + '"></i>');
                    boton.css({position: "absolute", left: 100, top: 30});
                    return boton;
                }, stop: function(e, ui) {
                    $(".view").resizable();
                    $( ".view" ).resizable( "destroy" );

                    /*   if ($(e.target).hasClass("dinamic_column")) {
                     items = $(e.target).children();
                     size = items.size();
                     md = 12 / size;
                     for (i = 1; i < 12; i++) {
                     items.removeClass("col-md-" + i);
                     }
                     items.addClass("col-md-" + md);


                     }
                     */

                }

            });



            if (t.helper.hasClass('widget_column') || t.helper.hasClass('tab_widget')) {
                $(".box").droppable({accept: "[data-nombre='animacion'], [data-nombre='borde']", hoverClass: "ui-dropable-placeholder", greedy: true,
                    drop: function(event, ui) {
                        if (ui.helper.attr('data-nombre') == 'borde') {
                            var borde = ui.helper.attr('data-borde');
                            $(this).attr('class', 'box box-element ui-draggable ui-droppable');
                            $(this).attr('class', borde + ' border_box box box-element ui-draggable ui-droppable');
                        }
                        else {
                            var animacion = ui.helper.attr('data-animacion');
                            //Reinicio los css
                            $(this).attr('class', 'box box-element ui-draggable ui-droppable');
                            $(this).attr('class', animacion + ' box box-element ui-draggable ui-droppable');
                            // $( this ).children("span:first").append("<a class='drag'><i class='fa fa-move'></i></a>");
                        }

                    }
                });
            }

        }});

        //Drag&Drop de animaciones
        $("[data-nombre='animacion']").draggable({helper: "clone", handle: ".drag, .preview"
            , drag: function(e, t) {
                //t.helper.width(400);
                t.helper.css('z-index', '9999');
                t.helper.css('background-color', 'black');
                t.helper.css('position', 'absolute');
                t.helper.find('.view').remove();
            }

        });

        //Drag&Drop de bordes
        $("[data-nombre='borde']").draggable({helper: "clone", handle: ".drag, .preview"
            , drag: function(e, t) {
                //t.helper.width(400);
                t.helper.css('z-index', '9999');
                t.helper.css('background-color', 'black');
                t.helper.css('position', 'absolute');
                t.helper.find('.view').remove();
            }

        });

        $(".box").droppable({accept: "[data-nombre='animacion'], [data-nombre='borde']", hoverClass: "ui-dropable-placeholder", greedy: true,
            drop: function(event, ui) {
                if (ui.helper.attr('data-nombre') == 'borde') {
                    var borde = ui.helper.attr('data-borde');
                    $(this).attr('class', 'box box-element ui-draggable ui-droppable');
                    $(this).attr('class', borde + ' border_box box box-element ui-draggable ui-droppable');
                }

                else {
                    var animacion = ui.helper.attr('data-animacion');
                    //Reinicio los css
                    $(this).attr('class', 'box box-element ui-draggable ui-droppable');
                    $(this).attr('class', animacion + ' box box-element ui-draggable ui-droppable');
                    // $( this ).children("span:first").append("<a class='drag'><i class='fa fa-move'></i></a>");
                }

            }
        });


        $(".fa-info-circle").mouseenter(function() {
            Alertify.log.create("info", $(this).data('ayuda'));
        });

        $("#tutorial").click(function() {
            introJs().start();
        });



        $("#edit").click(function() {
            $("body").removeClass("devpreview sourcepreview");
            $("body").addClass("edit");
            $("body").toggleClass("no-image");
            $('#sidebar-nav').css('right', '0px');
            removeMenuClasses();
            $(this).addClass("active");
            return false
        });

        $('.logoEstatico').click(function() {
            if (flag) {
                $(".logoEstatico").animate({
                    right: "+=135px"
                }, 400, function() {
                    // Animation complete.
                });
                flag = false;
            }

            else {
                flag = true;
                $(".logoEstatico").animate({
                    right: "-=135px"
                }, 400, function() {
                    // Animation complete.
                });

                $("body").removeClass("edit");
                $("body").addClass("devpreview sourcepreview");
                removeMenuClasses();
                $(this).addClass("active");
                $('#sidebar-nav').css('right', '-200px');
                return false;

            }


        });

        $("#clear").click(function(e) {
            e.preventDefault();
            clearDemo()
        });


        $(".nav-header").click(function() {
            $(".sidebar-nav .boxes, .sidebar-nav .rows").hide();

            /* Comentareado por si lenny se hecha para atras con la barra
             $(".nav-header i.fa-minus-square-o").removeClass("fa-minus-square-o").addClass('fa-plus-square-o');
             $(this).find('i.fa-plus-square-o').removeClass('fa-plus-square-o').addClass("fa-minus-square-o");
             */
            $(".nav-header img").attr('src', '/bundles/cms/images/iconos/agrupado.png');
            $(this).find('img').attr('src', '/bundles/cms/images/iconos/desplazado.png');
            $(this).next().slideDown();

        });
        removeElm();
        configurationElm();
        gridSystemGenerator();
//    setInterval(function() {
//        handleSaveLayout()
//    }, timerSave)


        setTimeout(function() {
            var propTwet = $('#twitterWidget').attr('data-widget-id');
            $("#idtwitter").attr("value", propTwet);
            var propLink = $('#likenidRender').attr('data-id');
            $("#linkedinurl").attr("value", propLink);
            var userpint = $("#pinterestRen").attr("href");
            //    var mipinit = userpint.replace('http://es.pinterest.com/', '');
            //   $("#userpinterest").attr("value", mipinit);
        }, 10000);


    });

    var saveLayout = function() {

    };




});
