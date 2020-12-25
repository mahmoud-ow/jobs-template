<template>
    <div class="d-flex align-items-center justify-content-center">
        <div class="slider-wrapper">
            <div class="slider-indicator-lg">
                <div class="progress progress-striped active">
                    <div
                        role="progressbar progress-striped"
                        class="progress-bar progress-bar-success "
                    >
                        <span></span>
                    </div>
                </div>
            </div>

            <div class="slider-content-wrapper">
                <div class="slider-controls-wrapper">
                    <div class="slider-controls">
                        <div class="slider-next">
                            <i class="fas">&#xf35a;</i>
                        </div>

                        <div class="slider-circles-wrapper">
                            <div class="slider-circles">
                                <div
                                    class="slider-circle slider-circle-3"
                                    data-circle="3"
                                ></div>
                                <div
                                    class="slider-circle slider-circle-2"
                                    data-circle="2"
                                ></div>
                                <div
                                    class="slider-circle slider-circle-1"
                                    data-circle="1"
                                ></div>
                            </div>
                        </div>
                        <div class="slider-previous">
                            <i class="fas">&#xf359;</i>
                        </div>
                    </div>
                    <!-- /.slider-controls -->
                </div>
                <!-- /.slider-controls-wrapper -->

                <div class="slider-content">
                    <div class="slider-layout slider-layout-3">
                        <div class="slider-layout-text">
                            <div>
                                <h1>هل تبحث عن وظيفة ؟</h1>
                                <p>أنت فى المكان المناسب</p>
                                <p>وظائف عديدة , مجالات مختلفة</p>
                            </div>
                        </div>
                        <div class="slider-layout-image">
                            <img
                                src="../../images/slider/slider-image-3.svg"
                                alt=""
                            />
                        </div>
                    </div>

                    <div class="slider-layout slider-layout-2">
                        <div class="slider-layout-text">
                            <div>
                                <h1>هل تبحث عن موظفين ؟</h1>
                                <p>أنت فى المكان المناسب</p>
                                <p>موظفين أكفاء فى مختلف المجالات</p>
                            </div>
                        </div>
                        <div class="slider-layout-image">
                            <img
                                src="../../images/slider/slider-image-2.svg"
                                alt=""
                            />
                        </div>
                    </div>

                    <div class="slider-layout slider-layout-1">
                        <div class="slider-layout-text">
                            <div>
                                <h1>منصة وظائف</h1>
                                <p>لخدمات التوظيف و الاعمال</p>
                                <p>خليك فى المضمون</p>
                            </div>
                        </div>
                        <div class="slider-layout-image">
                            <img
                                src="../../images/slider/slider-image-1.svg"
                                alt=""
                            />
                        </div>
                    </div>
                </div>
                <!-- /.slider-content -->
            </div>
            <!-- /.slider-content-wrapper -->
        </div>
    </div>
</template>

<script>
export default {
    data() {
        return {
            iterator: 1,
            slideDuration: 6000,
            sliderHandler: null
        };
    },
    mounted() {
        var self = this;
        self.sliderHandler = setInterval(function() {
            slide();
        }, self.slideDuration);

        $(".slider-next").on("click", function() {
            clearInterval(self.sliderHandler);
            if (self.iterator > 3) {
                self.iterator = 1;
            }
            slide();
            self.sliderHandler = setInterval(function() {
                slide();
            }, self.slideDuration);
        });

        $(".slider-previous").on("click", function() {
            clearInterval(self.sliderHandler);
            self.iterator = self.iterator - 2;
            if (parseInt(self.iterator) < 1) {
                self.iterator = 3;
            }
            slide();
            self.sliderHandler = setInterval(function() {
                slide();
            }, self.slideDuration);
        });

        $(".slider-circle").on("click", function() {
            clearInterval(self.sliderHandler);
            self.iterator = $(this).data("circle");
            slide();
            self.sliderHandler = setInterval(function() {
                slide();
            }, self.slideDuration);
        });

        function slide() {
            if (self.iterator > 3) {
                self.iterator = 1;
            }
            $(".slider-circle").removeClass("active-slider-circle");
            $(".slider-circle-" + self.iterator).addClass(
                "active-slider-circle"
            );
            $(".progress-bar").css({
                width: "0%",
                transition: "all 1s linear"
            });
            setTimeout(function() {
                $(".progress-bar").css({
                    width: "100%",
                    transition: "all 5s linear"
                });
            }, 1000);
            $(".slider-layout").removeClass("active-slider-layout");
            $(".slider-layout-" + self.iterator).addClass(
                "active-slider-layout"
            );
            self.iterator++;
            if (self.iterator > 4) {
                self.iterator = 1;
            }
        }
        slide();
    },
    beforeDestroy() {
        clearInterval(this.sliderHandler);
    }
};
</script>
<style lang="scss" scoped>
/* slider */
.slider-wrapper {
    height: calc(100vh - 56px);
    width: 100%;
    position: relative;
    border-bottom: thin solid #7070701a;
    display: flex;
    flex-direction: column;
    align-items: flex-start;
    background-color: #fff;
}
.slider-indicator-lg {
    width: 100%;
    height: 4px;
    margin: 0 auto;
    position: relative;
    box-shadow: inset 0 -1px 1px rgba(255, 255, 255, 0.3);
}

.slider-content-wrapper {
    display: flex;
    width: 100%;
    height: 100%;
}

.slider-controls-wrapper {
    display: flex;
    align-items: center;
    justify-content: center;
    width: 50px;
    margin-right: 20px;
}
.slider-controls {
    display: flex;
    transform: rotate(90deg);
}
.slider-circles-wrapper {
    flex: 1;
    justify-content: center;
    flex-wrap: wrap;
    align-items: center;
    display: flex;
}
.slider-circles {
    display: flex;
    justify-content: center;
    align-items: center;
}
.slider-circles > div {
    height: 26px;
    width: 26px;
    border-radius: 50%;
    border: medium solid #4ec839;
    margin: 5px;
    cursor: pointer;
}
.active-slider-circle {
    background-color: #737373;
}

.slider-next,
.slider-previous {
    display: flex;
    justify-content: center;
    align-items: center;
    cursor: pointer;
}

.slider-next i,
.slider-previous i {
    font-size: 36px !important;
    color: #4fc839;
}

.slider-content {
    flex: 1;
    display: flex;
    background-color: #fff;
    width: 100%;
    align-items: center;
    justify-content: center;
    position: relative;
}

.slider-layout {
    position: absolute;
    top: 0;
    right: 0;
    left: 0;
    margin: auto;
    width: calc(100% - 100px);
    height: 100%;
    display: flex;
    background-color: #fff;
    opacity: 0;
    transition: all 1s ease;
}
.active-slider-layout {
    opacity: 1;
}

.slider-layout > div {
    height: 100%;
    display: flex;
    justify-content: center;
    align-items: center;
}

.slider-layout-text {
    flex-direction: column;
}

.slider-layout-text h1 {
    font-size: 50px;
    color: #4ec839;
    margin-bottom: 20px;
    transform: translate3d(0px, -50px, 0);
    opacity: 0;
    transition: all 0.3s ease-in-out;
}
.slider-layout-text p {
    font-size: 36px;
    line-height: 1.5em;
    transform: translate3d(0px, 50px, 0);
    opacity: 0;
    transition: all 0.3s ease-in-out;
}
.slider-layout-text p:nth-of-type(1) {
    transition-delay: 0.5s;
}
.slider-layout-text p:nth-of-type(2) {
    transition-delay: 1s;
}

.active-slider-layout .slider-layout-text p,
.active-slider-layout .slider-layout-text h1 {
    transform: translate3d(0px, 0px, 0);
    opacity: 1;
}

.slider-layout-image {
    flex: 1;
}
.slider-layout-image img {
    max-height: 76%;
    max-width: 76%;
    object-fit: contain;
    height: 100%;
    width: 100%;
}

$brand-default: #b0bec5;
$brand-primary: #2196f3;
$brand-secondary: #323a45;
$brand-success: #4fc839;
$brand-warning: #ffd600;
$brand-info: #29b6f6;
$brand-danger: #ef1c1c;
$bg-light-gray: #f5f5f5;
.progress {
    height: 4px;
    background-color: $bg-light-gray;
    border-radius: 3px;
    box-shadow: none;
    &.progress-xs {
        height: 4px;
        margin-top: 5px;
    }
    &.progress-sm {
        height: 10px;
        margin-top: 5px;
    }
    &.progress-lg {
        height: 25px;
    }
    &.vertical {
        position: relative;
        width: 20px;
        height: 200px;
        display: inline-block;
        margin-right: 10px;
        > .progress-bar {
            width: 100% !important;
            position: absolute;
            bottom: 0;
        }
        &.progress-xs {
            width: 5px;
            margin-top: 5px;
        }
        &.progress-sm {
            width: 10px;
            margin-top: 5px;
        }
        &.progress-lg {
            width: 30px;
        }
    }
}

.progress-bar {
    background-color: $brand-primary;
    box-shadow: none;
    &.text-left {
        text-align: left;
        span {
            margin-left: 10px;
        }
    }
    &.text-right {
        text-align: right;
        span {
            margin-right: 10px;
        }
    }
}
@mixin gradient-striped($color: rgba(255, 255, 255, 0.15), $angle: 45deg) {
    background-image: -webkit-linear-gradient(
        $angle,
        $color 25%,
        transparent 25%,
        transparent 50%,
        $color 50%,
        $color 75%,
        transparent 75%,
        transparent
    );
    background-image: -o-linear-gradient(
        $angle,
        $color 25%,
        transparent 25%,
        transparent 50%,
        $color 50%,
        $color 75%,
        transparent 75%,
        transparent
    );
    background-image: linear-gradient(
        $angle,
        $color 25%,
        transparent 25%,
        transparent 50%,
        $color 50%,
        $color 75%,
        transparent 75%,
        transparent
    );
}

@-webkit-keyframes progress-bar-stripes {
    from {
        background-position: 40px 0;
    }
    to {
        background-position: 0 0;
    }
}

// Spec and IE10+
@keyframes progress-bar-stripes {
    from {
        background-position: 40px 0;
    }
    to {
        background-position: 0 0;
    }
}

@mixin animation($animation) {
    -webkit-animation: $animation;
    -o-animation: $animation;
    animation: $animation;
}
.progress.active .progress-bar,
.progress-bar.active {
    @include animation(progress-bar-stripes 2s linear infinite);
}
.progress-striped .progress-bar,
.progress-bar-striped {
    @include gradient-striped;
    background-size: 40px 40px;
}
@mixin progress-bar-variant($color) {
    background-color: $color;
}

.progress-bar-secondary {
    @include progress-bar-variant($brand-secondary);
}

.progress-bar-default {
    @include progress-bar-variant($brand-default);
}

.progress-bar-success {
    @include progress-bar-variant($brand-success);
}
.progress-bar {
    width: 0px;
    height: 4px;
}
</style>
