<template>
  <div class="component-wrapper">
    <div class="loading" :class="{ 'hide-loading': loaded }">
      <div class="lds-dual-ring"></div>
    </div>
    <div class="component-title">
      <h5>{{ tab.title }}</h5>
    </div>
    <div class="component-content">
      <table
        ref="table"
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
      </table>
    </div>
    <!-- /.component-content -->
  </div>
  <!-- /.component-wrapper -->
</template>

<script>
import { mapState } from "vuex";
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
      apiClient: axios.create({
        baseURL: "api/users",
        withCredentials: false,
        headers: {
          Accept: "application/json",
          "Content-Type": "application/json",
          "X-CSRF-TOKEN": window.csrf
        }
      }),
      table: null,
      loaded: false
    };
  },
  computed: {
    selected() {
      return this.activeTab == this.tabRef;
    },
    tabRef() {
      return this.tab.ref;
    },
    ...mapState(["user", "usersDTReload"])
  },
  watch: {
    usersDTReload: function() {
      if (this.$store.state[this.tabRef + "DTReload"]) {
        this.$store.dispatch("dataTableReloadState", {
          table_ref: this.tabRef,
          reload_state: false
        });
        this.table.ajax.reload(null, false);
      }
    },
    selected: function() {
      if (this.selected && this.table == null) {
        var self = this;

        $.fn.DataTable.ext.pager.numbers_length = 5;

        if (!this.loaded) {
          $(document).ready(function() {
            self.table = $(self.$refs.table).DataTable({
              responsive: true,
              fixedHeader: true,
              pageLength: 10,
              language,
              ajax: {
                url: "/api/users",
                type: "GET",
                data: {
                  request_by: "admin"
                }
              },
              columnDefs: [
                { orderable: true, targets: 0 },
                {
                  targets: [3, 2],
                  createdCell: function(td, cellData, rowData, row, col) {
                    $(td).addClass(" ltr");
                  }
                },
                { className: "no-pd", targets: [4] }
              ],
              columns: [
                { data: "id", name: "id", visible: false },
                { data: "username", name: "username" },
                { data: "phone", name: "phone" },
                { data: "email", name: "email" },
                {
                  data: "state",
                  render: function(data, type, full, meta) {
                    var state = "";
                    var newState;
                    if (full.state == 1) {
                      newState = 0;
                      state = "checked";
                    } else {
                      newState = 1;
                    }
                    if (type == "display") {
                      let stateInfo = {
                        id: full.id,
                        newState: newState,
                        tabRef: self.tabRef
                      };
                      return (
                        `
                      <div class="dt-switch-wrapper">
                        
                        <input id="checkbox_` +
                        full.id +
                        `" type="checkbox" class="checkbox user_state_checkbox_` +
                        full.id +
                        `" ` +
                        state +
                        `/>

                       <label style="direction:ltr;" for="checkbox_` +
                        full.id +
                        `" class="switch"
                         data-state-info='{"id": "` +
                        full.id +
                        `", "newState": "` +
                        newState +
                        `", "rowIndex": "` +
                        meta.row +
                        `"}'>
                          <span class="switch__circle"><span class="switch__circle-inner"></span></span>
                          <span class="switch__left">Off</span><span class="switch__right">On</span>
                        </label>

                      </div>`
                      );
                    } else {
                      return data;
                    }
                  }
                },
                {
                  sortable: false,
                  className: "tabel-actions",
                  data: "id",
                  render: function(data, type, full, meta) {
                    return (
                      `
                    <button class="send-message-to-user dt-btn bg-primary text-light d-none" data-user-info='{"id":"` +
                      data +
                      `", "username": "` +
                      full.username +
                      `"}'>
                        <i class="far fa-comment-dots"></i>
                    </button>

                    <button class="delete-user dt-btn bg-danger text-light" data-user-info='{"id":"` +
                      data +
                      `", "username": "` +
                      full.username +
                      `"}'>
                      <i class="far fa-trash-alt"></i>
                    </button>`
                    );
                  }
                }
              ],
              deferRender: true,
              initComplete: function() {
                self.loaded = true;
              },
              drawCallback: () => {
                setTimeout(() => {
                  self.table.responsive.recalc();
                  self.table.fixedHeader.adjust();
                }, 1);
                self.dtAttachMethods();
              }
            });

            self.table.on("responsive-display", function(
              e,
              datatable,
              row,
              showHide,
              update
            ) {
              self.dtAttachMethods();
            });
          });
        } // ? !this.loaded
      } else if (this.selected && this.table != null) {
        this.table.fixedHeader.enable();
        this.table.responsive.recalc();
      } else if (!this.selected && this.table != null) {
        this.table.fixedHeader.disable();
      }
    }
  },
  methods: {
    dtAttachMethods() {
      var self = this;
      // Off click Events
      $(".switch, .delete-user, .send-message-to-user").off("click");

      /* Aattach Events */
      $(".switch").on("click", function() {
        self.updateUserState($(this).data("state-info"));
      });

      $(".delete-user").on("click", function() {
        self.deleteUser($(this).data("user-info"));
      });

      $(".send-message-to-user").on("click", function() {
        self.sendMessageToUser($(this).data("user-info"));
      });
    },
    updateTableData(rowIndex, cellIndex, data) {
      this.table
        .cell({ row: rowIndex, column: cellIndex })
        .data(data)
        .invalidate();
    },
    updateUserState(data) {
      var xs_checkbox = $(".child .user_state_checkbox_" + data.id);
      xs_checkbox.attr("checked") == "checked"
        ? xs_checkbox.removeAttr("checked")
        : xs_checkbox.attr("checked", "checked");
      loading();
      this.apiClient
        .post("/change-state", data)
        .then(response => {
          window.Toast.fire({
            icon: response.data.status,
            title: response.data.message
          });
          if (response.data.status == "success") {
            this.updateTableData(parseInt(data.rowIndex), 4, data.newState);
          } else {
            this.table
              .cell({ row: parseInt(data.rowIndex), column: 4 })
              .invalidate();
            xs_checkbox.attr("checked") == "checked"
              ? xs_checkbox.removeAttr("checked")
              : xs_checkbox.attr("checked", "checked");
          }
          this.dtAttachMethods();
        })
        .catch(err => {
          window.Toast.fire({
            icon: "error",
            title: "خطأ سيرفر , قم بعمل ريفريش"
          });
          this.table
            .cell({ row: parseInt(data.rowIndex), column: 4 })
            .invalidate();
          xs_checkbox.attr("checked") == "checked"
            ? xs_checkbox.removeAttr("checked")
            : xs_checkbox.attr("checked", "checked");
          this.dtAttachMethods();
        });
    },
    sendMessageToUser(data) {
      var self = this;
      let modalPurpose = "sendMessageToUser";
      Swal.fire({
        title:
          '<span style="direction:ltr;"><i class="far fa-envelope"></i>&nbsp;' +
          data.username +
          "</span>",
        showCancelButton: true,
        confirmButtonText: "إرسال",
        cancelButtonText: "إلغاء",
        confirmButtonColor: "#3085d6",
        cancelButtonColor: "#d33",
        showLoaderOnConfirm: true,
        customClass: {
          header: "align-items-end",
          title: "align-items-center text-capitalize"
        },
        html:
          `
    <div id="` +
          modalPurpose +
          `_modal">
      <textarea class="form-control" style="direction:rtl ;text-align:canter !important;min-height:100px;" id="` +
          modalPurpose +
          `_textarea" placeholder="إكتب الرسالة هنا"></textarea>
    </div>
    `,
        preConfirm: () => {
          if (!$("#" + modalPurpose + "_textarea").val()) {
            Swal.showValidationMessage("بعض الحقول مطلوبة");
          } else {
            return self.apiClient
              .post("/" + data.id + "/messages", {
                message_content: $("#" + modalPurpose + "_textarea").val(),
                sender: self.user
              })
              .then(function(response) {
                if (response.data.status == "error") {
                  throw new Error(response.data.message);
                }
                return response.data;
              })
              .catch(error => {
                Swal.showValidationMessage(
                  `${error.toString().replace("Error: ", "")}`
                );
              });
          }
        },
        allowOutsideClick: () => !Swal.isLoading()
      }).then(response => {
        if (response.value) {
          
          window.Toast.fire({
            icon: response.value.status,
            title: response.value.message
          });
        }
      });
    },
    deleteUser(data) {
      var self = this;
      Swal.fire({
        title:
          "هل أنت متأكد من حذف ؟<p class='container mt-2 mb-0 text-capitalize text-danger'>" +
          data.username +
          " </p>",
        text: " لن تستطيع إستعادة المحذوف !",
        icon: "warning",
        showCancelButton: true,
        confirmButtonColor: "#3085d6",
        cancelButtonColor: "#d33",
        confirmButtonText: "نعم إحذف !",
        cancelButtonText: "إلغاء",
        customClass: {
          title: "flex-wrap justify-content-center"
        }
      }).then(result => {
        if (result.value) {
          loading();
          this.apiClient.delete("/delete/" + data.id).then(function(response) {
            if (response.data.status == "success") {
              self.table.ajax
                .reload(() => {
                  window.Toast.fire({
                    icon: response.data.status,
                    title: response.data.message
                  });
                }, false)
                .responsive.recalc()
                .columns.adjust();
            }
          });
        }
      });
    }
  },
  created() {},
  mounted() {}
};
</script>

<style lang="scss"></style>
