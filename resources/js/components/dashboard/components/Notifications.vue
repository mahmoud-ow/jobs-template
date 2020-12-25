<template>
  <div class="component-wrapper">
    <div class="loading" :class="{ 'hide-loading': loaded }">
      <div class="lds-dual-ring"></div>
    </div>
    <div class="component-title">
      <button @click="addNotification"><i class="fas fa-plus"></i></button>
      <h5>{{ tab.title }}</h5>
    </div>
    <div class="component-content">
      <table
        ref="table"
        class="display nowrap table table-striped table-hover table-bordered"
      >
        <thead class="">
          <tr>
            <th>delete_token</th>
            <th>الإشعار</th>
            <th>جمهور ( عدد )</th>
            <th>مشاهدات</th>
            <th>التوقيت</th>
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
        baseURL: "api/notifications",
        withCredentials: false,
        headers: {
          Accept: "application/json",
          "Content-Type": "application/json",
          "X-CSRF-TOKEN": window.csrf
        }
      }),
      table: null,
      loaded: false,
      notification_audience: [
        {
          label: "الجميع",
          language: "universal"
        },
        {
          label: "اللغة العربية",
          language: "ar"
        },
        {
          label: "اللغة الإنجليزية",
          language: "en"
        }
      ]
    };
  },
  computed: {
    selected() {
      return this.activeTab == this.tabRef;
    },
    tabRef() {
      return this.tab.ref;
    },
    ...mapState(["user"])
  },
  watch: {
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
                url: "/api/notifications",
                type: "GET",
                data: {
                  request_by: "admin"
                }
              },
              columnDefs: [
                { targets: 0, orderable: true },
                { targets: 4, orderable: false }
              ],
              order: [[0, "desc"]],
              columns: [
                { data: "id", name: "id", visible: false },
                { data: "content", name: "content" },
                { data: "count", name: "count" },
                { data: "views", name: "views" },
                { data: "created_at", name: "created_at" },

                {
                  sortable: false,
                  className: "tabel-actions no-pd",
                  data: "notification_token",
                  render: function(data, type, full, meta) {
                    return (
                      `
                    <button class="edit-notification dt-btn bg-primary text-light" data-notification-token='` +
                      data +
                      `'>
                        <i class="far fa-edit"></i>
                    </button>

                    <button class="delete-notification dt-btn bg-danger text-light" data-notification-token='` +
                      data +
                      `'>
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
      $(".delete-notification, .edit-notification").off("click");

      /* Aattach Events */
      $(".delete-notification").on("click", function() {
        self.deleteNotification($(this).data("notification-token"));
      });

      $(".edit-notification").on("click", function() {
        self.editNotification($(this).data("notification-token"));
      });
    },
    addNotification() {
      var self = this;
      var notification_audience = "";
      if (this.notification_audience.length > 1) {
        notification_audience +=
          "<option disabled selected>حدد الجمهور ( وفقا للغة )</option>";
      }
      this.notification_audience.forEach(audience => {
        notification_audience +=
          '<option value="' +
          audience.language +
          '">' +
          audience.label +
          "</option>";
      });

      Swal.fire({
        title: "إرسال إشعار",
        showCancelButton: true,
        confirmButtonText: "إرسال",
        cancelButtonText: "إلغاء",
        confirmButtonColor: "#3085d6",
        cancelButtonColor: "#d33",
        showLoaderOnConfirm: true,
        html: `
        <div id="add_notification_modal">
          <select class="custom-select mb-3" id="add_notification_audience">
            ${notification_audience}
          </select>
          <textarea class="form-control" style="direction:rtl ;text-align:canter !important;min-height:100px;" id="add_notification_textarea" placeholder="إكتب الإشعار هنا"></textarea>
        </div>
        `,
        preConfirm: () => {
          var notification = $("#add_notification_textarea").val();
          var audience = $("#add_notification_audience").val();

          if (!notification) {
            Swal.showValidationMessage("قم بكتابة الإشعار !");
          } else if (audience == null) {
            Swal.showValidationMessage("رجاء تحديد الجمهور");
          } else {
            return self.apiClient
              .post("/", {
                notification_content: notification,
                notification_audience: audience,
                notification_source: "admin"
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
      }).then(result => {
        if (result.value) {
          self.table.ajax
            .reload(() => {
              window.Toast.fire({
                icon: result.value.status,
                title: result.value.message
              });
            }, false)
            .responsive.recalc()
            .columns.adjust();
        }
      });
    },

    editNotification(notification_token) {
      var self = this;
      var notification_token = notification_token;
      loading();

      this.apiClient
        .get("/" + notification_token, {
          params: {
            request_by: "admin"
          }
        })
        .then(function(response) {
          if (response.data.status == "success") {
            Swal.fire({
              title: "تعديل إشعار",
              showCancelButton: true,
              confirmButtonText: "حفظ",
              cancelButtonText: "إلغاء",
              confirmButtonColor: "#3085d6",
              cancelButtonColor: "#d33",
              showLoaderOnConfirm: true,
              html:
                `
            <div id="edit_notification_modal">
              <textarea class="form-control" style="text-align:center;min-height:100px;" id="edit_notification_textarea">` +
                response.data.content +
                `</textarea>
            </div>`,

              preConfirm: () => {
                if (!$("#edit_notification_textarea").val()) {
                  Swal.showValidationMessage("بعض الحقول مطلوبة");
                } else {
                  return self.apiClient
                    .put("/" + notification_token, {
                      notification: $("#edit_notification_textarea").val()
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
                self.table.ajax
                  .reload(() => {
                    window.Toast.fire({
                      icon: response.value.status,
                      title: response.value.message
                    });
                  }, false)
                  .responsive.recalc()
                  .columns.adjust();
              }
            });
          } else {
            window.Toast.fire({
              icon: response.data.status,
              title: response.data.message
            });
          }
        });
    },

    deleteNotification(notification_token) {
      var self = this;
      var notification_token = notification_token;
      Swal.fire({
        icon: "warning",
        title: "هل أنت متأكد من الحذف ؟",
        text: " لن تستطيع إستعادة المحذوف !",
        confirmButtonText: "نعم إحذف !",
        cancelButtonText: "إلغاء",
        showCancelButton: true,
        confirmButtonColor: "#3085d6",
        cancelButtonColor: "#d33"
      }).then(result => {
        if (result.value) {
          loading();
          self.apiClient
            .delete("/" + notification_token)
            .then(function(response) {
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
              } else {
                window.Toast.fire({
                  icon: response.data.status,
                  title: response.data.message
                });
              }
            });
        }
      });
    }
  },
  beforeMount () {
    // axios requests for chat
    var self = this;
    this.apiClient.interceptors.request.use(
      config => {
        self.$store.state.runningRequests += 1;
        return config;
      },
      error => {
        self.$store.state.runningRequests > 0
          ? (self.$store.state.runningRequests -= 1)
          : null;
        return Promise.reject(error);
      }
    );
    this.apiClient.interceptors.response.use(
      response => {
        self.$store.state.runningRequests > 0
          ? (self.$store.state.runningRequests -= 1)
          : null;
        return response;
      },
      error => {
        self.$store.state.runningRequests > 0
          ? (self.$store.state.runningRequests -= 1)
          : null;
        return Promise.reject(error.message);
      }
    );
  }
};
</script>

<style></style>
