<template>
    <div class="component-wrapper">
        <button @click="reload">Reload</button>
        <div class="loading" :class="{ 'hide-loading': loaded }">
            <div class="lds-dual-ring"></div>
        </div>
        <div class="component-content">
            <table
                ref="table"
                id="users-table"
                class="display nowrap table table-striped table-hover table-bordered"
            >
                <thead class="">
                    <tr>
                        <th>id</th>
                        <th>إسم المستخدم</th>
                        <th>الهاتف</th>
                        <th>البريد</th>
                        <th>الحالة</th>
                        <th>عمليات</th>
                    </tr>
                </thead>
                <tbody></tbody>
            </table>
        </div>
        <!-- /.component-content -->
    </div>
    <!-- /.component-wrapper -->
</template>

<script>
import { mapState, mapActions } from "vuex";
const language = {
    sEmptyTable: "ليست هناك بيانات متاحة في الجدول",
    sLoadingRecords: "جارٍ التحميل...",
    sProcessing: "جارٍ التحميل...",
    sLengthMenu: "أظهر _MENU_ مدخلات",
    sZeroRecords: "لم يعثر على أية سجلات",
    sInfo: "إظهار _START_ إلى _END_ من أصل _TOTAL_ مدخل",
    sInfoEmpty: "يعرض 0 إلى 0 من أصل 0 سجل",
    sInfoFiltered: "(منتقاة من مجموع _MAX_ مُدخل)",
    sInfoPostFix: "",
    sSearch: "ابحث:",
    sUrl: "",
    oPaginate: {
        sFirst: "الأول",
        sPrevious: "السابق",
        sNext: "التالي",
        sLast: "الأخير"
    },
    oAria: {
        sSortAscending: ": تفعيل لترتيب العمود تصاعدياً",
        sSortDescending: ": تفعيل لترتيب العمود تنازلياً"
    }
};
export default {
    props: ["tab", "activeTab", "tabs"],
    data() {
        return {
            table: null,
            loaded: false
        };
    },
    computed: {
        ...mapState("usersDTReload"),
        selected() {
            return this.activeTab == this.tab.ref;
        }
    },
    watch: {
        usersDTReload: {
            deep: true,
            handler(newVal) {
                console.log("queryParameter changed");
            }
        }
    },
    methods: {
        reload() {
            this.$store.dispatch("reloadDataTable", this.tab.ref);
            console.log(this.$store.state.dataTables);
            // this.table.ajax.reload(null, false);
        }
    },
    created() {
        this.$store.dispatch("addDataTable", this.tab.ref);
        var self = this;
        /* if (!this.loaded) { */
        $(document).ready(function() {
            const table = $("#users-table").DataTable({
                responsive: true,
                fixedHeader: true,
                pageLength: 10,
                language,
                ajax: {
                    url: "/api/users",
                    type: "GET"
                },
                columnDefs: [],
                columns: [
                    { data: "id", name: "id", visible: false },
                    { data: "username", name: "username" },
                    { data: "phone", name: "phone" },
                    { data: "email", name: "email" },
                    {
                        sortable: false,
                        data: "state",
                        render: function(data, type, full, meta) {
                            var state = "";
                            var changeState;
                            if (full.state == 1) {
                                changeState = 0;
                                state = "checked";
                            } else {
                                changeState = 1;
                            }
                            return (
                                '<div style="direction:ltr;"><input onchange="App.UserController().changeState(' +
                                full.id +
                                ", " +
                                changeState +
                                ')" id="checkbox_' +
                                full.id +
                                '" type="checkbox" class="checkbox" style="display:none;" ' +
                                state +
                                '/><label for="checkbox_' +
                                full.id +
                                '" class="switch" style="margin:auto;"><span class="switch__circle"><span class="switch__circle-inner"></span></span><span class="switch__left">Off</span><span class="switch__right">On</span></label></div>'
                            );
                        }
                    },
                    {
                        sortable: false,
                        className: "tabel-actions",
                        data: "id",
                        render: function(data, type, full, meta) {
                            /* window.func = self.myFunc; */
                            return (
                                `<button class="dt-btn" onclick="App.$store.dispatch('auth/checkOut',` +
                                full.id +
                                `)"  style="background-color: #f44336;">
                                        <i class="material-icons">delete_forever</i></button><button class="dt-btn" style="background-color: #3f51b5;"><i class="far fa-comment-dots"></i></button>`
                            );
                        }
                    }
                ],
                deferRender: true,
                initComplete: function() {
                    self.loaded = true;
                }
            });
        });
        /* } */ // ? !this.loaded
    },
    mounted() {}
};
</script>

<style lang="scss"></style>
