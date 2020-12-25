<template>
  <div class="component-wrapper">
    <div class="loading" :class="{ 'hide-loading': loaded }">
      <div class="lds-dual-ring"></div>
    </div>
    <div class="component-title">
      <button @click="addMetaTag"><i class="fas fa-plus"></i></button>
      <h5>{{ tab.title }}</h5>
    </div>
    <div class="component-content">
      <table
        ref="table"
        class="display nowrap table table-striped table-hover table-bordered"
      >
        <thead class="">
          <tr>
            <th>ID</th>
            <th>وسم الميتا</th>
            <th>عمليات</th>
          </tr>
        </thead>
      </table>
    </div>
    <!-- <button @click="loadUsers">Load Users</button> -->
  </div>
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
  props: ["tab", "activeTab"],
  data() {
    return {
      apiClient: axios.create({
        baseURL: "api/meta-tags",
        withCredentials: false,
        headers: {
          Accept: "application/json",
          "Content-Type": "application/json",
          "X-CSRF-TOKEN": window.csrf
        }
      }),
      loaded: false,
      table: null
    };
  },
  computed: {
    selected() {
      return this.activeTab == this.tab.ref;
    },
    tabRef() {
      return this.tab.ref;
    },
    ...mapState(["user", "usersDTReload"])
  },
  methods: {
    dtAttachMethods() {
      var self = this;
      // Off click Events
      $(".delete-meta-tag, .edit-meta-tag").off("click");

      /* Aattach Events */
      $(".edit-meta-tag").on("click", function() {
        self.editMetaTag($(this).data("meta-id"));
      });

      $(".delete-meta-tag").on("click", function() {
        self.deleteMetaTag($(this).data("meta-id"));
      });
    },
    addMetaTag() {
      var self = this;
      let modalPurpose = "addMetaTag";

      Swal.fire({
        title: "إضافة وسم Meta",
        showCancelButton: true,
        confirmButtonText: "إضافة",
        cancelButtonText: "إلغاء",
        confirmButtonColor: "#3085d6",
        cancelButtonColor: "#d33",
        customClass: {
          title: "flex-wrap justify-content-center"
        },
        showLoaderOnConfirm: true,
        html:
          `
        <div id="` +
          modalPurpose +
          `_modal">
          <textarea class="form-control" style="direction:ltr;text-align:left;min-height:100px;" id="` +
          modalPurpose +
          `_textarea"><meta property="" content="" /></textarea>
        </div>
        `,
        preConfirm: () => {
          if (!$("#" + modalPurpose + "_textarea").val()) {
            Swal.showValidationMessage("بعض الحقول مطلوبة");
          } else {
            return self.apiClient
              .post("/", {
                meta: $("#" + modalPurpose + "_textarea").val()
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
    },
    editMetaTag(id) {
      var self = this;
      let modalPurpose = "editMetaTag";

      loading();

      self.apiClient.get("/" + id).then(function(response) {
        if (response.data.status == "success") {
         

          Swal.fire({
            title: "تعديل وسم Meta",
            showCancelButton: true,
            confirmButtonText: "حفظ",
            cancelButtonText: "إلغاء",
            confirmButtonColor: "#3085d6",
            cancelButtonColor: "#d33",
            customClass: {
              title: "flex-wrap justify-content-center"
            },
            showLoaderOnConfirm: true,
            html:
              `
            <div id="` +
              modalPurpose +
              `_modal">
              <textarea class="form-control" style="direction:ltr;text-align:left;min-height:100px;" id="` +
              modalPurpose +
              `_textarea">` +
              response.data.meta.meta_tag +
              `</textarea>
            </div>
              `,
            preConfirm: () => {
              if (!$("#" + modalPurpose + "_textarea").val()) {
                Swal.showValidationMessage("بعض الحقول مطلوبة");
              } else {
                return self.apiClient
                  .put("/" + response.data.meta.id, {
                    meta: $("#" + modalPurpose + "_textarea").val()
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
    deleteMetaTag(id) {
      var self = this;
      Swal.fire({
        title: "هل أنت متأكد من الحذف ؟",
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
          this.apiClient.delete("/" + id).then(function(response) {
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
  watch: {
    selected: function() {
      if (this.selected && this.table == null) {
        var self = this;

        // dt pagination length
        $.fn.DataTable.ext.pager.numbers_length = 5;
        if (!this.loaded) {
          $(document).ready(function() {
            self.table = $(self.$refs.table).DataTable({
              responsive: true,
              fixedHeader: true,
              pageLength: 10,
              language,
              ajax: {
                url: "/api/meta-tags",
                type: "GET",
                data: {
                  request_by: "admin"
                }
              },
              order: [[0, "desc"]],
              columnDefs: [
                { orderable: true, targets: 0 },
                {
                  targets: [1],
                  createdCell: function(td, cellData, rowData, row, col) {
                    $(td).addClass(" ltr");
                  }
                },
                { className: "no-pd", targets: [2] }
              ],
              columns: [
                { data: "id", name: "id", visible: false },
                { data: "meta_tag", name: "meta_tag" },

                {
                  sortable: false,
                  className: "tabel-actions",
                  data: "id",
                  render: function(data, type, full, meta) {
                    return (
                      `
                    <button class="edit-meta-tag dt-btn bg-primary text-light" data-meta-id='` +
                      data +
                      `'>
                        <i class="far fa-edit"></i>
                    </button>

                    <button class="delete-meta-tag dt-btn bg-danger text-light" data-meta-id='` +
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
      } else {
        if (this.table != null) {
          this.table.fixedHeader.disable();
        }
      }
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
