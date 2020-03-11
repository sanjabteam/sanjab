/*!

 =========================================================
 * Material Dashboard - v2.1.1
 =========================================================

 * Product Page: https://www.creative-tim.com/product/material-dashboard
 * Copyright 2018 Creative Tim (http://www.creative-tim.com)

 * Designed by www.invisionapp.com Coded by www.creative-tim.com

 =========================================================

 * The above copyright notice and this permission notice shall be included in all copies or substantial portions of the Software.

 */

var breakCards = true;

var searchVisible = 0;
var transparent = true;

var transparentDemo = true;
var fixedTop = false;

var mobile_menu_visible = 0,
    mobile_menu_initialized = false,
    toggle_initialized = false,
    bootstrap_nav_initialized = false;

var seq = 0,
    delays = 80,
    durations = 500;
var seq2 = 0,
    delays2 = 80,
    durations2 = 500;

$(document).ready(function() {
    $("body").bootstrapMaterialDesign();

    $sidebar = $(".sidebar");

    md.initSidebarsCheck();

    window_width = $(window).width();

    // check if there is an image set for the sidebar's background
    md.checkSidebarImage();

    //    Activate bootstrap-select
    if ($(".selectpicker").length != 0) {
        $(".selectpicker").selectpicker();
    }

    //  Activate the tooltips
    $('[rel="tooltip"]').tooltip();

    $(".form-control")
        .on("focus", function() {
            $(this)
                .parent(".input-group")
                .addClass("input-group-focus");
        })
        .on("blur", function() {
            $(this)
                .parent(".input-group")
                .removeClass("input-group-focus");
        });

    // remove class has-error for checkbox validation
    $(
        'input[type="checkbox"][required="true"], input[type="radio"][required="true"]'
    ).on("click", function() {
        if ($(this).hasClass("error")) {
            $(this)
                .closest("div")
                .removeClass("has-error");
        }
    });
});

$(document).on("click", ".navbar-toggler", function() {
    $toggle = $(this);

    if (mobile_menu_visible == 1) {
        $("html").removeClass("nav-open");

        $(".close-layer").remove();
        setTimeout(function() {
            $toggle.removeClass("toggled");
        }, 400);

        mobile_menu_visible = 0;
    } else {
        setTimeout(function() {
            $toggle.addClass("toggled");
        }, 430);

        var $layer = $('<div class="close-layer"></div>');

        if ($("body").find(".main-panel").length != 0) {
            $layer.appendTo(".main-panel");
        } else if ($("body").hasClass("off-canvas-sidebar")) {
            $layer.appendTo(".wrapper-full-page");
        }

        setTimeout(function() {
            $layer.addClass("visible");
        }, 100);

        $layer.click(function() {
            $("html").removeClass("nav-open");
            mobile_menu_visible = 0;

            $layer.removeClass("visible");

            setTimeout(function() {
                $layer.remove();
                $toggle.removeClass("toggled");
            }, 400);
        });

        $("html").addClass("nav-open");
        mobile_menu_visible = 1;
    }
});

// activate collapse right menu when the windows is resized
$(window).resize(function() {
    md.initSidebarsCheck();

    // reset the seq for charts drawing animations
    seq = seq2 = 0;

    setTimeout(function() {
        md.initDashboardPageCharts();
    }, 500);
});

md = {
    misc: {
        navbar_menu_visible: 0,
        active_collapse: true,
        disabled_collapse_init: 0
    },

    checkSidebarImage: function() {
        $sidebar = $(".sidebar");
        image_src = $sidebar.data("image");

        if (image_src !== undefined) {
            sidebar_container =
                '<div class="sidebar-background" style="background-image: url(' +
                image_src +
                ') "/>';
            $sidebar.append(sidebar_container);
        }
    },

    showNotification: function(from, align) {
        type = ["", "info", "danger", "success", "warning", "rose", "primary"];

        color = Math.floor(Math.random() * 6 + 1);

        $.notify(
            {
                icon: "add_alert",
                message:
                    "Welcome to <b>Material Dashboard Pro</b> - a beautiful admin panel for every web developer."
            },
            {
                type: type[color],
                timer: 3000,
                placement: {
                    from: from,
                    align: align
                }
            }
        );
    },

    initDocumentationCharts: function() {
        if (
            $("#dailySalesChart").length != 0 &&
            $("#websiteViewsChart").length != 0
        ) {
            /* ----------==========     Daily Sales Chart initialization For Documentation    ==========---------- */

            dataDailySalesChart = {
                labels: ["M", "T", "W", "T", "F", "S", "S"],
                series: [[12, 17, 7, 17, 23, 18, 38]]
            };

            optionsDailySalesChart = {
                lineSmooth: Chartist.Interpolation.cardinal({
                    tension: 0
                }),
                low: 0,
                high: 50, // creative tim: we recommend you to set the high sa the biggest value + something for a better look
                chartPadding: {
                    top: 0,
                    right: 0,
                    bottom: 0,
                    left: 0
                }
            };

            var dailySalesChart = new Chartist.Line(
                "#dailySalesChart",
                dataDailySalesChart,
                optionsDailySalesChart
            );

            var animationHeaderChart = new Chartist.Line(
                "#websiteViewsChart",
                dataDailySalesChart,
                optionsDailySalesChart
            );
        }
    },

    initFormExtendedDatetimepickers: function() {
        $(".datetimepicker").datetimepicker({
            icons: {
                time: "fa fa-clock-o",
                date: "fa fa-calendar",
                up: "fa fa-chevron-up",
                down: "fa fa-chevron-down",
                previous: "fa fa-chevron-left",
                next: "fa fa-chevron-right",
                today: "fa fa-screenshot",
                clear: "fa fa-trash",
                close: "fa fa-remove"
            }
        });

        $(".datepicker").datetimepicker({
            format: "MM/DD/YYYY",
            icons: {
                time: "fa fa-clock-o",
                date: "fa fa-calendar",
                up: "fa fa-chevron-up",
                down: "fa fa-chevron-down",
                previous: "fa fa-chevron-left",
                next: "fa fa-chevron-right",
                today: "fa fa-screenshot",
                clear: "fa fa-trash",
                close: "fa fa-remove"
            }
        });

        $(".timepicker").datetimepicker({
            //          format: 'H:mm',    // use this format if you want the 24hours timepicker
            format: "h:mm A", //use this format if you want the 12hours timpiecker with AM/PM toggle
            icons: {
                time: "fa fa-clock-o",
                date: "fa fa-calendar",
                up: "fa fa-chevron-up",
                down: "fa fa-chevron-down",
                previous: "fa fa-chevron-left",
                next: "fa fa-chevron-right",
                today: "fa fa-screenshot",
                clear: "fa fa-trash",
                close: "fa fa-remove"
            }
        });
    },

    initSliders: function() {
        // Sliders for demo purpose
        var slider = document.getElementById("sliderRegular");

        noUiSlider.create(slider, {
            start: 40,
            connect: [true, false],
            range: {
                min: 0,
                max: 100
            }
        });

        var slider2 = document.getElementById("sliderDouble");

        noUiSlider.create(slider2, {
            start: [20, 60],
            connect: true,
            range: {
                min: 0,
                max: 100
            }
        });
    },

    initSidebarsCheck: function() {
        if ($(window).width() <= 991) {
            if ($sidebar.length != 0) {
                md.initRightMenu();
            }
        }
    },

    checkFullPageBackgroundImage: function() {
        $page = $(".full-page");
        image_src = $page.data("image");

        if (image_src !== undefined) {
            image_container =
                '<div class="full-page-background" style="background-image: url(' +
                image_src +
                ') "/>';
            $page.append(image_container);
        }
    },

    initDashboardPageCharts: function() {
        if (
            $("#dailySalesChart").length != 0 ||
            $("#completedTasksChart").length != 0 ||
            $("#websiteViewsChart").length != 0
        ) {
            /* ----------==========     Daily Sales Chart initialization    ==========---------- */

            dataDailySalesChart = {
                labels: ["M", "T", "W", "T", "F", "S", "S"],
                series: [[12, 17, 7, 17, 23, 18, 38]]
            };

            optionsDailySalesChart = {
                lineSmooth: Chartist.Interpolation.cardinal({
                    tension: 0
                }),
                low: 0,
                high: 50, // creative tim: we recommend you to set the high sa the biggest value + something for a better look
                chartPadding: {
                    top: 0,
                    right: 0,
                    bottom: 0,
                    left: 0
                }
            };

            var dailySalesChart = new Chartist.Line(
                "#dailySalesChart",
                dataDailySalesChart,
                optionsDailySalesChart
            );

            md.startAnimationForLineChart(dailySalesChart);

            /* ----------==========     Completed Tasks Chart initialization    ==========---------- */

            dataCompletedTasksChart = {
                labels: ["12p", "3p", "6p", "9p", "12p", "3a", "6a", "9a"],
                series: [[230, 750, 450, 300, 280, 240, 200, 190]]
            };

            optionsCompletedTasksChart = {
                lineSmooth: Chartist.Interpolation.cardinal({
                    tension: 0
                }),
                low: 0,
                high: 1000, // creative tim: we recommend you to set the high sa the biggest value + something for a better look
                chartPadding: {
                    top: 0,
                    right: 0,
                    bottom: 0,
                    left: 0
                }
            };

            var completedTasksChart = new Chartist.Line(
                "#completedTasksChart",
                dataCompletedTasksChart,
                optionsCompletedTasksChart
            );

            // start animation for the Completed Tasks Chart - Line Chart
            md.startAnimationForLineChart(completedTasksChart);

            /* ----------==========     Emails Subscription Chart initialization    ==========---------- */

            var dataWebsiteViewsChart = {
                labels: [
                    "J",
                    "F",
                    "M",
                    "A",
                    "M",
                    "J",
                    "J",
                    "A",
                    "S",
                    "O",
                    "N",
                    "D"
                ],
                series: [
                    [542, 443, 320, 780, 553, 453, 326, 434, 568, 610, 756, 895]
                ]
            };
            var optionsWebsiteViewsChart = {
                axisX: {
                    showGrid: false
                },
                low: 0,
                high: 1000,
                chartPadding: {
                    top: 0,
                    right: 5,
                    bottom: 0,
                    left: 0
                }
            };
            var responsiveOptions = [
                [
                    "screen and (max-width: 640px)",
                    {
                        seriesBarDistance: 5,
                        axisX: {
                            labelInterpolationFnc: function(value) {
                                return value[0];
                            }
                        }
                    }
                ]
            ];
            var websiteViewsChart = Chartist.Bar(
                "#websiteViewsChart",
                dataWebsiteViewsChart,
                optionsWebsiteViewsChart,
                responsiveOptions
            );

            //start animation for the Emails Subscription Chart
            md.startAnimationForBarChart(websiteViewsChart);
        }
    },

    initMinimizeSidebar: function() {
        $("#minimizeSidebar").click(function() {
            var $btn = $(this);

            if (md.misc.sidebar_mini_active == true) {
                $("body").removeClass("sidebar-mini");
                md.misc.sidebar_mini_active = false;
            } else {
                $("body").addClass("sidebar-mini");
                md.misc.sidebar_mini_active = true;
            }

            // we simulate the window Resize so the charts will get updated in realtime.
            var simulateWindowResize = setInterval(function() {
                window.dispatchEvent(new Event("resize"));
            }, 180);

            // we stop the simulation of Window Resize after the animations are completed
            setTimeout(function() {
                clearInterval(simulateWindowResize);
            }, 1000);
        });
    },

    checkScrollForTransparentNavbar: debounce(function() {
        if ($(document).scrollTop() > 260) {
            if (transparent) {
                transparent = false;
                $(".navbar-color-on-scroll").removeClass("navbar-transparent");
            }
        } else {
            if (!transparent) {
                transparent = true;
                $(".navbar-color-on-scroll").addClass("navbar-transparent");
            }
        }
    }, 17),

    initRightMenu: debounce(function() {
        $sidebar_wrapper = $(".sidebar-wrapper");

        if (!mobile_menu_initialized) {
            // $navbar = $("nav")
            //     .find(".navbar-collapse")
            //     .children(".navbar-nav");

            // mobile_menu_content = "";

            // nav_content = $navbar.html();

            // nav_content =
            //     '<ul class="nav navbar-nav nav-mobile-menu">' +
            //     nav_content +
            //     "</ul>";

            // navbar_form = $("nav")
            //     .find(".navbar-form")
            //     .get(0).outerHTML;

            // $sidebar_nav = $sidebar_wrapper.find(" > .nav");

            // // insert the navbar form before the sidebar list
            // $nav_content = $(nav_content);
            // $navbar_form = $("<div></div>");
            // $nav_content.insertBefore($sidebar_nav);
            // // $navbar_form.insertBefore($nav_content);

            // $(".sidebar-wrapper .dropdown .dropdown-menu > li > a").click(
            //     function(event) {
            //         event.stopPropagation();
            //     }
            // );

            // // simulate resize so all the charts/maps will be redrawn
            // window.dispatchEvent(new Event("resize"));

            // mobile_menu_initialized = true;
        } else {
            if ($(window).width() > 991) {
                // reset all the additions that we made for the sidebar wrapper only if the screen is bigger than 991px
                // $sidebar_wrapper.find(".navbar-form").remove();
                // $sidebar_wrapper.find(".nav-mobile-menu").remove();

                //mobile_menu_initialized = false;
            }
        }
    }, 200),

    startAnimationForLineChart: function(chart) {
        chart.on("draw", function(data) {
            if (data.type === "line" || data.type === "area") {
                data.element.animate({
                    d: {
                        begin: 600,
                        dur: 700,
                        from: data.path
                            .clone()
                            .scale(1, 0)
                            .translate(0, data.chartRect.height())
                            .stringify(),
                        to: data.path.clone().stringify(),
                        easing: Chartist.Svg.Easing.easeOutQuint
                    }
                });
            } else if (data.type === "point") {
                seq++;
                data.element.animate({
                    opacity: {
                        begin: seq * delays,
                        dur: durations,
                        from: 0,
                        to: 1,
                        easing: "ease"
                    }
                });
            }
        });

        seq = 0;
    },
    startAnimationForBarChart: function(chart) {
        chart.on("draw", function(data) {
            if (data.type === "bar") {
                seq2++;
                data.element.animate({
                    opacity: {
                        begin: seq2 * delays2,
                        dur: durations2,
                        from: 0,
                        to: 1,
                        easing: "ease"
                    }
                });
            }
        });

        seq2 = 0;
    },

    initVectorMap: function() {
        var mapData = {
            AU: 760,
            BR: 550,
            CA: 120,
            DE: 1300,
            FR: 540,
            GB: 690,
            GE: 200,
            IN: 200,
            RO: 600,
            RU: 300,
            US: 2920
        };

        $("#worldMap").vectorMap({
            map: "world_mill_en",
            backgroundColor: "transparent",
            zoomOnScroll: false,
            regionStyle: {
                initial: {
                    fill: "#e4e4e4",
                    "fill-opacity": 0.9,
                    stroke: "none",
                    "stroke-width": 0,
                    "stroke-opacity": 0
                }
            },

            series: {
                regions: [
                    {
                        values: mapData,
                        scale: ["#AAAAAA", "#444444"],
                        normalizeFunction: "polynomial"
                    }
                ]
            }
        });
    }
};

// Returns a function, that, as long as it continues to be invoked, will not
// be triggered. The function will be called after it stops being called for
// N milliseconds. If `immediate` is passed, trigger the function on the
// leading edge, instead of the trailing.

function debounce(func, wait, immediate) {
    var timeout;
    return function() {
        var context = this,
            args = arguments;
        clearTimeout(timeout);
        timeout = setTimeout(function() {
            timeout = null;
            if (!immediate) func.apply(context, args);
        }, wait);
        if (immediate && !timeout) func.apply(context, args);
    };
}

$(document).ready(function() {
    $().ready(function() {
        $sidebar = $(".sidebar");

        $sidebar_img_container = $sidebar.find(".sidebar-background");

        $full_page = $(".full-page");

        $sidebar_responsive = $("body > .navbar-collapse");

        window_width = $(window).width();

        fixed_plugin_open = $(
            ".sidebar .sidebar-wrapper .nav li.active a p"
        ).html();

        if (window_width > 767 && fixed_plugin_open == "Dashboard") {
            if ($(".fixed-plugin .dropdown").hasClass("show-dropdown")) {
                $(".fixed-plugin .dropdown").addClass("open");
            }
        }

        $(".fixed-plugin a").click(function(event) {
            // Alex if we click on switch, stop propagation of the event, so the dropdown will not be hide, otherwise we set the  section active
            if ($(this).hasClass("switch-trigger")) {
                if (event.stopPropagation) {
                    event.stopPropagation();
                } else if (window.event) {
                    window.event.cancelBubble = true;
                }
            }
        });

        $(".fixed-plugin .active-color span").click(function() {
            $full_page_background = $(".full-page-background");

            $(this)
                .siblings()
                .removeClass("active");
            $(this).addClass("active");

            var new_color = $(this).data("color");

            if ($sidebar.length != 0) {
                $sidebar.attr("data-color", new_color);
            }

            if ($full_page.length != 0) {
                $full_page.attr("filter-color", new_color);
            }

            if ($sidebar_responsive.length != 0) {
                $sidebar_responsive.attr("data-color", new_color);
            }
        });

        $(".fixed-plugin .background-color .badge").click(function() {
            $(this)
                .siblings()
                .removeClass("active");
            $(this).addClass("active");

            var new_color = $(this).data("background-color");

            if ($sidebar.length != 0) {
                $sidebar.attr("data-background-color", new_color);
            }
        });

        $(".fixed-plugin .img-holder").click(function() {
            $full_page_background = $(".full-page-background");

            $(this)
                .parent("li")
                .siblings()
                .removeClass("active");
            $(this)
                .parent("li")
                .addClass("active");

            var new_image = $(this)
                .find("img")
                .attr("src");

            if (
                $sidebar_img_container.length != 0 &&
                $(".switch-sidebar-image input:checked").length != 0
            ) {
                $sidebar_img_container.fadeOut("fast", function() {
                    $sidebar_img_container.css(
                        "background-image",
                        'url("' + new_image + '")'
                    );
                    $sidebar_img_container.fadeIn("fast");
                });
            }

            if (
                $full_page_background.length != 0 &&
                $(".switch-sidebar-image input:checked").length != 0
            ) {
                var new_image_full_page = $(
                    ".fixed-plugin li.active .img-holder"
                )
                    .find("img")
                    .data("src");

                $full_page_background.fadeOut("fast", function() {
                    $full_page_background.css(
                        "background-image",
                        'url("' + new_image_full_page + '")'
                    );
                    $full_page_background.fadeIn("fast");
                });
            }

            if ($(".switch-sidebar-image input:checked").length == 0) {
                var new_image = $(".fixed-plugin li.active .img-holder")
                    .find("img")
                    .attr("src");
                var new_image_full_page = $(
                    ".fixed-plugin li.active .img-holder"
                )
                    .find("img")
                    .data("src");

                $sidebar_img_container.css(
                    "background-image",
                    'url("' + new_image + '")'
                );
                $full_page_background.css(
                    "background-image",
                    'url("' + new_image_full_page + '")'
                );
            }

            if ($sidebar_responsive.length != 0) {
                $sidebar_responsive.css(
                    "background-image",
                    'url("' + new_image + '")'
                );
            }
        });

        $(".switch-sidebar-image input").change(function() {
            $full_page_background = $(".full-page-background");

            $input = $(this);

            if ($input.is(":checked")) {
                if ($sidebar_img_container.length != 0) {
                    $sidebar_img_container.fadeIn("fast");
                    $sidebar.attr("data-image", "#");
                }

                if ($full_page_background.length != 0) {
                    $full_page_background.fadeIn("fast");
                    $full_page.attr("data-image", "#");
                }

                background_image = true;
            } else {
                if ($sidebar_img_container.length != 0) {
                    $sidebar.removeAttr("data-image");
                    $sidebar_img_container.fadeOut("fast");
                }

                if ($full_page_background.length != 0) {
                    $full_page.removeAttr("data-image", "#");
                    $full_page_background.fadeOut("fast");
                }

                background_image = false;
            }
        });

        $(".switch-sidebar-mini input").change(function() {
            $body = $("body");

            $input = $(this);

            if (md.misc.sidebar_mini_active == true) {
                $("body").removeClass("sidebar-mini");
                md.misc.sidebar_mini_active = false;
            } else {
                setTimeout(function() {
                    $("body").addClass("sidebar-mini");

                    md.misc.sidebar_mini_active = true;
                }, 300);
            }

            // we simulate the window Resize so the charts will get updated in realtime.
            var simulateWindowResize = setInterval(function() {
                window.dispatchEvent(new Event("resize"));
            }, 180);

            // we stop the simulation of Window Resize after the animations are completed
            setTimeout(function() {
                clearInterval(simulateWindowResize);
            }, 1000);
        });
    });
});
