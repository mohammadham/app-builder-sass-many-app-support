import api from "../api";

class AdminSubscribeService {

  getTransactions(offset) {
    return api.get(`admin/transactions/list?offset=${offset}`, {});
  }

  getUserSubscribes(offset, user_id) {
    return api.get(`admin/transactions/user_subscribe?offset=${offset}&user_id=${user_id}`, {});
  }

  getSubscribe(external_uid, offset) {
    return api.get(`admin/transactions/subscribe?external_uid=${external_uid}&offset=${offset}`, {});
  }

}

export default new AdminSubscribeService();
