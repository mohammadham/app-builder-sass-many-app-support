import api from "../api";

class ConfigService {
  getLocaleAppConfig() {
    return api.get(`/data/config.json`, {});
  }

  getGlobalConfig() {
    return api.get(`public/settings/config`, {});
  }
}

export default new ConfigService();
