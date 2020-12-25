<template>
  <div>
    <div id="toggleChat" @click.stop="$store.state.visibleChat = true">
      <div>
        <i class="fab fa-facebook-messenger"></i>
        <div id="toggleChat-counter">
          0
        </div>
      </div>
    </div>

    <div id="chat-wrapper" :class="{ 'visible-chat': visibleChat }" @click.stop>
      <!-- <div id="chat-container"> -->

      <div
        id="active-conversation-container"
        :class="{ visible: conversation_visible }"
      >
        <div class="chat-header">
          <div class="chat-header-label">
            <span class="chat-header-label-text"></span>
          </div>

          <div class="chat-header-icon d-none" id="scroll-down-go-down">
            <i class="fas fa-chevron-down"></i>
          </div>
          <div
            class="chat-header-icon"
            id="back-to-conversations"
            @click="conversation_visible = false"
          >
            <i class="fas fa-chevron-left"></i>
          </div>

          <div
            class="chat-header-icon chat-close"
            @click.stop="$store.state.visibleChat = false"
          >
            <i class="fas fa-times"></i>
          </div>
        </div>

        <div id="active-conversation" data-active-conversation-id="0">
          <div id="active-conversation-replies"></div>

          <div id="new-partner-replies-go-down">
            <i class="far fa-envelope"></i>
          </div>
        </div>

        <div id="chat-form-container">
          <div id="chat-users-info">
            users here
          </div>

          <div id="chat-form-box">
            <div id="uploadingImage">
              <div>
                <div class="progress" id="chat-upload-progress">
                  <div
                    class="progress-bar progress-bar-striped progress-bar-animated"
                    role="progressbar"
                    aria-valuenow="75"
                    aria-valuemin="0"
                    aria-valuemax="100"
                    style="width: 75%"
                  ></div>
                </div>
              </div>
            </div>

            <div id="chat-add-image">
              <i class="fas fa-plus"></i>
            </div>

            <form
              id="chat_reply_form"
              @submit.prevent="addNewConversationReply"
            >
              <div id="chat-reply-input">
                <textarea
                  @keydown="addReplyHandler"
                  dir="auto"
                  v-model="newConversationReply"
                  class="form-control"
                ></textarea>
              </div>

              <div id="chat-reply-btn">
                <button type="submit">
                  <i class="fas fa-paper-plane"></i>
                </button>
              </div>
            </form>
          </div>
        </div>
      </div>

      <div id="all-conversations">
        <div class="chat-header">
          <div class="chat-header-label">
            <i class="far fa-comments"></i> &nbsp; المحادثات
          </div>
          <div
            class="chat-header-icon chat-close"
            @click="$store.state.visibleChat = false"
          >
            <i class="fas fa-times"></i>
          </div>
        </div>

        <div id="conversations-container">
          <div id="conversations-rows">
            <div id="chat-ask-guest" v-if="!$store.state.user.logged_in">
              <div>
                <p style="text-align: center;">لتتمكن من إستخدام المحادثات</p>

                <div class="mt-5 mb-3">
                  <router-link
                    :to="{ name: 'login' }"
                    class="btn btn-md btn-primary"
                  >
                    <i style="font-size:18px" class="fas">&#xf502;</i>
                    &nbsp; سجل دخول
                    <span class="sr-only">(current)</span>
                  </router-link>
                </div>

                <div>
                  <router-link
                    :to="{ name: 'register' }"
                    class="btn btn-md btn-success"
                  >
                    <i style="font-size:18px" class="fas">&#xf234;</i>
                    &nbsp; أنشئ حساب
                    <span class="sr-only">(current)</span>
                  </router-link>
                </div>
              </div>
            </div>
          </div>
        </div>
      </div>

      <div id="conversations-bottom-label">owcode.com</div>

      <!-- </div> -->
    </div>
  </div>
</template>

<script>
import { mapState, mapActions } from "vuex";
import PerfectScrollbar from "perfect-scrollbar";
export default {
  data() {
    return {
      conversationsContainer: null, // will be set in mount hook
      conversationsContainer_ps: null, // perfect scroll handler for init/update
      activeConversationContainer: null,
      activeConversationContainer_ps: null, // perfect scroll handler for init/update
      conversation_visible: false, // current conversation view
      fetchPreviousConversationsRequests: 0,
      newConversationReply: "",
      loadPreviousConversationRepliesRequest: 0
    };
  },
  computed: {
    ...mapState({
      user: state => state.user,
      visibleChat: state => state.visibleChat,
      activeConversation_id: state => state.conversation.activeConversation_id,
      conversation_partner_id: state =>
        state.conversation.conversation_partner_id,
      conversation_last_partner_reply_id: state =>
        state.conversation.conversation_last_partner_reply_id
    })
  },
  watch: {
    activeConversation_id: function() {
      this.fetchConversation();
    },
    user: function() {
      this.fetchConversations();
    }
  },
  methods: {
    ...mapActions({
      updateActiveConversationId: "conversation/updateActiveConversationId",
      updateConversationPartnerId: "conversation/updateConversationPartnerId",
      updatePartnerLastReplyId: "conversation/updatePartnerLastReplyId"
    }),
    chatLoader: function() {
      return '<div class="chat-loader"><div class="lds-ring"><div></div><div></div><div></div><div></div></div></div>';
    },
    perfectScrollInit: function(element) {
      return new PerfectScrollbar(element, {
        wheelSpeed: 1,
        wheelPropagation: false,
        minScrollbarLength: 20
      });
    },

    fetchConversations: function() {
      var self = this;
      if (this.user.id > 0) {
        $("#conversations-rows").html(this.chatLoader());
        var params = {
          user_id: self.user.id
        };
        self.$store
          .dispatch("conversation/fetchConversations", params)
          .then(function(response) {
            if (response.data.conversations != "") {
              $("#conversations-rows").html(response.data.conversations);
              self.selectConversation();
            } else {
              $("#conversations-rows").html(
                '<div class="no-conversations-found">لا يوجد محادثات</div>'
              );
            }

            if (self.conversationsContainer_ps) {
              self.conversationsContainer_ps.update();
            } else {
              self.conversationsContainer_ps = self.perfectScrollInit(
                self.conversationsContainer
              );
            }

            self.conversationsContainer.addEventListener(
              "ps-y-reach-end",
              function() {
                self.fetchPreviousConversations();
              }
            );
            // self.checkForNewConversations();
          });
      }
    },
    fetchPreviousConversations: function() {
      var self = this;
      if (self.fetchPreviousConversationsRequests === 0) {
        self.fetchPreviousConversationsRequests = 1;

        var last_conversation = $(".conversation-row:last-of-type");

        var params = {
          user_id: self.user.id,
          last_conversation_id: last_conversation.attr("data-conversation-id"),
          last_conversation_row_id: last_conversation.attr(
            "data-conversation-row-id"
          )
        };

        self.$store
          .dispatch("conversation/fetchPreviousConversations", params)
          .then(function(response) {
            var currentScroll = self.conversationsContainer.scrollTop;
            if (response.data.conversations != "") {
              $("#conversations-rows").append(response.data.conversations);
              self.fetchPreviousConversationsRequests = 0;
              self.selectConversation();
            } else {
              $("#conversations-rows").append(
                "<p class='no-previous-conversations'>إنتهت المحادثات</p>"
              );
            }

            self.conversationsContainer_ps.update();
          });
        /* /axios */
      }
    },

    selectConversation: function() {
      var self = this;
      $(".conversation-row").off("click");
      $(".conversation-row").click(function() {
        var partner_id = $(this).attr("data-partner-id");
        var conversation_id = $(this).attr("data-conversation-id");

        $("#active-conversation-container .chat-header-label-text").html(
          $(this)
            .find(".conversation-row-partner-name")
            .text()
        );

        self.conversation_visible = true;
        self.updateActiveConversationId(conversation_id);
        self.updateConversationPartnerId(partner_id);
        self.loadPreviousConversationRepliesRequest = 0;
        // the conversation will be loaded in watch hook
      });
    },
    fetchConversation: function() {
      var self = this;

      // reset the conversation container ( scroll position + events )
      self.activeConversationContainer.removeEventListener(
        "ps-y-reach-start",
        self.fetchPreviousConversationReplies
      );
      self.activeConversationContainer.scrollTop = 0;

      // display loading animation
      $("#active-conversation-replies").html(self.chatLoader());

      var params = {
        user_id: self.user.id,
        conversation_id: self.activeConversation_id,
        partner_id: self.conversation_partner_id
      };

      self.$store
        .dispatch("conversation/fetchConversation", params)
        .then(function(response) {
          // init/update perfect scroll
          if (self.activeConversationContainer_ps) {
            self.activeConversationContainer_ps.update();
          } else {
            self.activeConversationContainer_ps = self.perfectScrollInit(
              self.activeConversationContainer
            );
          }

          // append conversation replies
          $("#active-conversation-replies").html(response.data.replies);

          var partnerReplies = document.querySelectorAll(".partner_user_reply");
          if (partnerReplies.length > 0) {
            var lastPartnerReply = partnerReplies[
              partnerReplies.length - 1
            ].getAttribute("data-reply-id");
          } else {
            var lastPartnerReply = 0;
          }
          self.updatePartnerLastReplyId(lastPartnerReply);

          // scroll to latest
          self.activeConversationContainer.scrollTop =
            self.activeConversationContainer.scrollHeight;
          $("#chat_reply_form textarea").focus();

          // reset not seen to none
          $(
            '.conversation-row[data-conversation-id="' +
              response.data.conversation_id +
              '"] .conversation-row-notseen-messages-count span'
          ).text("0");

          // attach events to the container
          self.activeConversationContainer.addEventListener(
            "ps-y-reach-start",
            self.fetchPreviousConversationReplies
          );
          self.activeConversationContainer.addEventListener(
            "ps-scroll-down",
            function() {
              if (
                self.activeConversationContainer.scrollTop >
                self.activeConversationContainer.scrollHeight -
                  $("#active-conversation").height() -
                  200
              ) {
                $("#scroll-down-go-down").removeClass("visible");
              } else {
                $("#scroll-down-go-down").addClass("visible");
              }
            }
          );
          self.activeConversationContainer.addEventListener(
            "ps-scroll-up",
            function() {
              $("#scroll-down-go-down").removeClass("visible");
            }
          );
        });
    },

    addReplyHandler(e) {
      if (e.keyCode === 13 && !e.shiftKey) {
        e.preventDefault();
        this.addNewConversationReply();
      }
    },
    addNewConversationReply: function() {
      var self = this;

      var params = {
        auth_id: self.user.id,
        conversation_id: self.activeConversation_id,
        conversation_reply: self.newConversationReply
      };
      // clear textarea after submit
      self.newConversationReply = null;

      self.$store
        .dispatch("conversation/addNewConversationReply", params)
        .then(function(response) {
          if (response.data.error == 1) {
          } else {
            $("#active-conversation-replies").append(response.data.reply);
            self.activeConversationContainer.scrollTop =
              self.activeConversationContainer.scrollHeight;
            $("#chat_reply_form textarea").focus();
            // clearTimeout(window.checkForNewConversationsHandler);
            // window.checkForNewConversationsHandler = window.checkForNewConversations();
          }
          //console.log( JSON.stringify(response.data) );
        });
    },

    fetchPreviousConversationReplies: function() {
      var self = this;

      if (self.loadPreviousConversationRepliesRequest == 0) {
        self.loadPreviousConversationRepliesRequest = 1;

        var last_viewed_reply = $(
          "#active-conversation-replies .conversation_reply_row:first-of-type"
        );

        var params = {
          auth_id: self.user.id,
          conversation_id: self.activeConversation_id,
          last_viewed_reply_id: last_viewed_reply.attr("data-reply-id")
        };

        self.$store
          .dispatch("conversation/fetchPreviousConversationReplies", params)
          .then(function(response) {
            if (response.data.replies) {
              $("#active-conversation-replies").prepend(response.data.replies);
              self.activeConversationContainer_ps.update();
              self.activeConversationContainer.scrollTop = last_viewed_reply.position().top;
              self.loadPreviousConversationRepliesRequest = 0;
            } else {
              $("#active-conversation-replies").prepend(
                "<p class='no-previous-replies'>إنتهت المحادثة</p>"
              );
              self.activeConversationContainer_ps.update();
            }
          });
      }
    },
    fetchNewConversationReplies() {
      if (window.active_chat != 0) {
        console.log(window.active_chat);
      } else {
        console.log(0);
      }

      if (window.active_chat != 0) {
        var partnerReplies = document.querySelectorAll(".partner_user_reply");

        if (partnerReplies.length > 0) {
          var lastPartnerReply = partnerReplies[
            partnerReplies.length - 1
          ].getAttribute("data-reply-id");
        } else {
          var lastPartnerReply = 0;
        }

        axios
          .get(
            "/chat/conversations/new_replies/" +
              window.active_chat +
              "/" +
              lastPartnerReply
          )
          .then(function(response) {
            if (response.data.replies) {
              if (
                window.activeConversation.scrollTop ==
                window.activeConversation.scrollHeight -
                  $("#active-conversation").height()
              ) {
                $("#active-conversation-replies").append(response.data.replies);
                window.activeConversation.scrollTop =
                  window.activeConversation.scrollHeight;
              } else {
                $("#active-conversation-replies").append(response.data.replies);
                $("#new-partner-replies-go-down").addClass("visible");
                setTimeout(function() {
                  $("#new-partner-replies-go-down").removeClass("visible");
                }, 3000);
              }
            }
          })
          .catch(error => {})
          .then(() => {
            // always executed
            setTimeout(
              window.loadPartnerNewReplies,
              window.active_chat_refresh_timer
            );
          });
        /* /axios */
      } else {
        setTimeout(
          window.loadPartnerNewReplies,
          window.active_chat_refresh_timer
        );
      }
    }
  },

  mounted() {
    $(document).ready(() => {
      this.conversationsContainer = document.querySelector(
        "#conversations-container"
      );
      this.activeConversationContainer = document.querySelector(
        "#active-conversation"
      );
      this.fetchConversations();

      $("body").on("click", () => {
        this.$store.state.visibleChat = false;
      });
    });
  }
};
</script>

<style lang="scss">
/* chat toggle */
#toggleChat {
  height: 50px;
  width: 50px;
  font-size: 40px;
  background-color: #343a40;
  color: #fff;
  cursor: pointer;
  box-shadow: 0 0 2px black;
  border-radius: 50%;
  display: flex;
  justify-content: center;
  align-items: center;
  position: fixed;
  bottom: 12px;
  left: 12px;
  z-index: 999;
  transition: all 0.3s ease;
}
.hideToggleChat {
  visibility: hidden;
  opacity: 0;
  transform: translate3d(-160px, 0px, 0px);
}
#toggleChat:hover {
  box-shadow: 0 0 5px black;
}
#toggleChat > div {
  position: relative;
  height: 50px;
  width: 50px;
  display: flex;
  justify-content: center;
  align-items: center;
}
#toggleChat-counter {
  position: absolute;
  top: 0;
  right: 0;
  background-color: #e3342f;
  height: 20px;
  line-height: 10px;
  font-size: 14px;
  display: flex;
  align-items: center;
  padding: 0 5px;
  border-radius: 50%;
}

// blur-content
.blur-content {
  position: fixed;
  top: 0;
  left: 0;
  width: 100%;
  height: 100vh;
  z-index: 1019;
  background-color: rgba(36, 36, 36, 0.35);
  visibility: hidden;
  opacity: 0;
  transition: all 0.3s ease;
  cursor: pointer;
}
.show-blur-content {
  visibility: visible;
  opacity: 1;
}
// chat wrapper
#chat-wrapper {
  position: fixed;
  bottom: 6px;
  left: 10px;
  font-family: "Tajawal", sans-serif;
  transition: all 0.4s ease;
  visibility: hidden;
  opacity: 0;
  transform: translate3d(-320px, 0px, 0px);
  z-index: 1019;
  /* width: 0%;
  height: 0%; */
  min-height: 400px;
  max-height: 50vh;
  width: 320px;
  height: 100%;
  box-shadow: 0 0 15px #80808063;
  border-top: 3px solid #97c371;
  border-radius: 5px 5px 3px 3px;
  overflow: hidden;

  .no-conversations-found {
    display: flex;
    align-items: center;
    justify-content: center;
    height: 100%;
  }

  /* #chat-container {
    height: 100%;
    width: 100%;
    background-color: #fff;
    padding: 0 5px;
    display: flex;
    flex-direction: column;
    box-shadow: 0 0 15px #80808063;
    position: relative;
    border-top: 3px solid #97c371;
    border-radius: 5px 5px 3px 3px;
    overflow: hidden;
  } */

  #all-conversations {
    position: absolute;
    max-height: 100%;
    background-color: #fff;
    flex: 1;
    box-shadow: inset 0 0 3px hsla(0, 0%, 50.2%, 0.12);
    display: flex;
    flex-direction: column;
    width: 100%;
    left: 0;
    top: 0;
    height: calc(100% - 25px);
  }
  #conversations-container {
    direction: ltr;
    background-color: #efefef;
    flex: 1;
    position: relative;
  }
  #conversations-rows {
    height: 100%;
  }
  /* chat header */
  .chat-header {
    display: flex;
    color: #495057;
    font-size: 20px;
    border-bottom: 1px solid #ddd;
    position: relative;
    z-index: 1;
  }
  .chat-header > div {
    height: 50px;
    display: flex;
    align-items: center;
  }
  .chat-header-label {
    flex: 1;
    padding: 0 10px;
    text-transform: capitalize;
    font-size: 18px;
    overflow: hidden;
    white-space: nowrap;
    font-weight: 600;
  }
  .chat-header-icon {
    width: 50px;
    justify-content: center;
    cursor: pointer;
  }

  #chat-ask-guest {
    height: 100%;
    display: flex;
    align-items: center;
    flex-wrap: wrap;
  }
  #chat-ask-guest > div {
    text-align: center;
    margin: 10px auto;
  }

  #conversations-bottom-label {
    text-align: center;
    box-shadow: 0 -2px 3px rgba(128, 128, 128, 0.12);
    position: absolute;
    height: 26px;
    display: flex;
    align-items: center;
    justify-content: center;
    bottom: 0;
    left: 0;
    width: 100%;
    background-color: #97c371;
    color: #fff;
    padding-bottom: 3px;
  }

  // chat loader
  .chat-loader {
    justify-content: center;
    display: flex;
    align-items: center;
    height: 100%;
    position: absolute;
    width: 100%;
  }
  .lds-ring {
    display: inline-block;
    position: relative;
    width: 80px;
    height: 80px;
  }
  .lds-ring div {
    box-sizing: border-box;
    display: block;
    position: absolute;
    width: 64px;
    height: 64px;
    margin: 8px;
    border: 8px solid #fff;
    border-radius: 50%;
    animation: lds-ring 1.2s cubic-bezier(0.5, 0, 0.5, 1) infinite;
    border-color: #495057 transparent transparent transparent;
  }
  .lds-ring div:nth-child(1) {
    animation-delay: -0.45s;
  }
  .lds-ring div:nth-child(2) {
    animation-delay: -0.3s;
  }
  .lds-ring div:nth-child(3) {
    animation-delay: -0.15s;
  }
  @keyframes lds-ring {
    0% {
      transform: rotate(0deg);
    }
    100% {
      transform: rotate(360deg);
    }
  }

  // conversation row
  .conversation-row {
    height: 50px;
    border-bottom: 1px solid #ddd;
    overflow: hidden;
    display: flex;
    direction: ltr;
    color: #2196f3;
    cursor: pointer;
    padding: 0 10px;
    align-items: center;
    > div {
      height: 50px;
      display: flex;
      align-items: center;
    }
    .conversation-row-partner-image {
      width: 40px;
      height: 40px !important;
      overflow: hidden;
      border-radius: 50%;
    }
    .conversation-row-partner-image img {
      width: 80%;
      height: 80%;
      opacity: 0.3;
      object-fit: cover;
    }
    .conversation-row-partner-name {
      flex: 1;
      padding: 0 10px;
      text-transform: capitalize;
      font-size: 16px;
      font-weight: bold;
      white-space: nowrap;
      overflow: hidden;
      text-overflow: ellipsis;
    }
    .conversation-row-notseen-messages-count {
      padding: 0 5px;
    }
  }
  .conversation-row:hover {
    background-color: #f7f7f7;
  }

  // no more previous replies
  .no-previous-replies,
  .no-previous-conversations {
    text-align: center;
    padding: 10px 0;
    text-shadow: 0px 0px 2px #9c27b000;
    color: #f44336;
    font-weight: 600;
    font-size: 15px;
    border-top: 1px solid #94949469;
    background-color: #dddddd;
  }
  .no-previous-conversations {
    margin-bottom: 0;
  }
}

.visible-chat {
  visibility: visible !important;
  opacity: 1 !important;
  transform: translate3d(0px, 0px, 0px) !important;
  z-index: 100;
  display: block;
}
.visible {
}
// active-conversation
#active-conversation-container {
  position: absolute;
  top: 0;
  left: 0;
  width: 100%;
  height: 100%;
  z-index: 1;
  display: flex;
  flex-direction: column;
  background-color: #fff;
  visibility: hidden;
  z-index: 2;

  .mdl-menu__container {
    right: 8px !important;
  }
  .chat-header-label-icon {
    width: 32px;
    justify-content: center;
    text-align: center;
  }

  .mdl-menu {
    padding: 0;
    border-top: 2px solid #495057;
  }

  .mdl-menu__item {
    display: flex;
    align-items: center;
  }
}

#active-conversation-container .mdl-menu__container {
  right: 8px !important;
}
#active-conversation-container .chat-header-label-icon {
  width: 32px;
  justify-content: center;
  text-align: center;
}

#active-conversation-container .mdl-menu {
  padding: 0;
  border-top: 2px solid #495057;
}

#active-conversation-container .mdl-menu__item {
  display: flex;
  align-items: center;
}

.chat-header-icon {
  width: 50px;
  justify-content: center;
  cursor: pointer;
}
.chat-header-icon:hover {
  color: salmon;
}
.chat-header-label-text {
  direction: ltr;
  display: block;
  max-width: 100%;
  overflow: hidden;
  text-overflow: ellipsis;
  text-align: center;
  padding-right: 5px;
}

// active-conversation
#active-conversation-container {
  position: absolute;
  top: 0;
  left: 0;
  width: 100%;
  height: 100%;
  z-index: 1;
  display: flex;
  flex-direction: column;
  background-color: #fff;
  visibility: hidden;
  z-index: 2;
}

// active conversation
#active-conversation {
  direction: ltr;
  background-color: #efefef;
  flex: 1;
  position: relative;
  height: 100%;
}

#active-conversation-replies {
  padding-top: 1px;
  margin-top: -1px;
  margin-bottom: -1px;
  padding-bottom: 1px;
}

// conversation_reply_row
.conversation_reply_row {
  display: flex;
  align-items: center;
  padding: 0 15px;
  margin: 20px 0;
}

.conversation_reply_row span {
  padding: 3px 10px;
  border-radius: 3px;
  position: relative;
  max-width: 70%;
  white-space: pre-line;
  display: flex;
}

.current_user_reply {
  justify-content: flex-end;
}

.current_user_reply span {
  background-color: #495057;
  color: #f8fafc;
  display: flex;
}
.current_user_reply span::after {
  content: "";
  display: block;
  position: absolute;
  right: -10px;
  top: calc(50% - 5px);
  border: 5px solid #495057;
  border-top-color: transparent;
  border-right-color: transparent;
  border-bottom-color: transparent;
}

.partner_user_reply {
  font-weight: 600;
  /* justify-content: flex-end; */
}
.partner_user_reply span {
  background-color: #ddd;
}
.partner_user_reply span::before {
  content: "";
  display: block;
  position: absolute;
  left: -10px;
  top: calc(50% - 5px);
  border: 5px solid #ddd;
  border-top-color: transparent;
  border-left-color: transparent;
  border-bottom-color: transparent;
}

// no more previous replies
.no-previous-replies,
.no-previous-conversations {
  text-align: center;
  padding: 10px 0;
  text-shadow: 0px 0px 2px #9c27b000;
  color: #f44336;
  font-weight: 600;
  font-size: 15px;
  border-top: 1px solid #94949469;
  background-color: #dddddd;
}
.no-previous-conversations {
  margin-bottom: 0;
}

/* chat form */
#chat-form-container {
  position: relative;
}
#chat-form-container #chat-form-box {
  display: flex;
  border-top: 1px solid #ddd;
  padding: 10px;
  align-items: center;
}
#chat-form-container form {
  display: flex;
  width: 100%;
  align-items: center;
}
#chat-reply-input {
  flex: 1;
  margin: 0 5px;
}
#chat-reply-input textarea {
  resize: none;
  overflow: hidden;
  line-height: 16px !important;
  padding-top: 10px !important;
  text-shadow: 0 0 0;
  font-size: 15px;
  height: 40px;
}
#chat-add-image {
  width: 36px;
  display: flex;
  align-items: center;
  justify-content: center;
  color: #495057;
  font-size: 20px;
  height: 100%;
}

#chat-add-image .mdl-menu__container {
  right: 8px !important;
}

#chat-reply-btn button {
  width: 40px;
  display: flex;
  align-items: center;
  justify-content: center;
  color: #fff;
  font-size: 20px;
  background-color: #495057;
  height: 40px;
  border-radius: 50%;
  border: none;
  box-shadow: 1px 1px 3px #673ab7;
}

// new partner replies notification
#new-partner-replies-go-down {
  position: fixed;
  bottom: 70px;
  left: 20px;
  width: 36px;
  height: 36px;
  box-shadow: 0 0 3px grey;
  display: flex;
  justify-content: center;
  align-items: center;
  border-radius: 50%;
  background-color: #495057;
  color: #fff;
  cursor: pointer;
  visibility: hidden;
  opacity: 0;
  transition: all 0.3s ease;
}

#new-partner-replies-go-down:hover {
  color: #00bcd4;
}

// chat users info for admin view
#chat-users-info {
  display: none;
  position: absolute;
  background-color: #fff;
  left: 0;
  top: 0;
  width: 100%;
  height: 100%;
  z-index: 1;
}

.admin-view #chat-users-info {
  display: block;
}
</style>