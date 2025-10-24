<!doctype html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="description" content="">
    <meta name="author" content="Impeltech LLC">
    <meta name="generator" content="Hugo 0.88.1">
    <title>Flangapp PRO install</title>
    <!-- Bootstrap core CSS -->
    <link href="https://cdn.jsdelivr.net/npm/vuetify@3.5.6/dist/vuetify.min.css" rel="stylesheet">
    <script src="https://cdn.jsdelivr.net/npm/vue@3.4.20/dist/vue.global.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/vuetify@3.5.6/dist/vuetify.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/axios/dist/axios.min.js"></script>
</head>
<body>
<div id="app">
    <v-layout>
        <v-main>
            <v-toolbar color="transparent">
                <div class="d-flex justify-center align-center align-self-center" style="width: 100vw">
                    <svg width="34" height="34" viewBox="0 0 34 34" fill="none" xmlns="http://www.w3.org/2000/svg">
                        <mask id="mask0_402_2" style="mask-type:alpha" maskUnits="userSpaceOnUse" x="0" y="0" width="34" height="34">
                            <path d="M0 17C0 9.06475 0 5.09713 2.42605 2.60034C2.48331 2.54142 2.54142 2.48331 2.60034 2.42605C5.09713 0 9.06475 0 17 0C24.9352 0 28.9029 0 31.3997 2.42605C31.4586 2.48331 31.5167 2.54142 31.5739 2.60034C34 5.09713 34 9.06475 34 17C34 24.9352 34 28.9029 31.5739 31.3997C31.5167 31.4586 31.4586 31.5167 31.3997 31.5739C28.9029 34 24.9352 34 17 34C9.06475 34 5.09713 34 2.60034 31.5739C2.54142 31.5167 2.48331 31.4586 2.42605 31.3997C0 28.9029 0 24.9352 0 17Z" fill="url(#paint0_linear_402_2)"/>
                        </mask>
                        <g mask="url(#mask0_402_2)">
                            <path d="M0 17C0 9.06475 0 5.09713 2.42605 2.60034C2.48331 2.54142 2.54142 2.48331 2.60034 2.42605C5.09713 0 9.06475 0 17 0C24.9352 0 28.9029 0 31.3997 2.42605C31.4586 2.48331 31.5167 2.54142 31.5739 2.60034C34 5.09713 34 9.06475 34 17C34 24.9352 34 28.9029 31.5739 31.3997C31.5167 31.4586 31.4586 31.5167 31.3997 31.5739C28.9029 34 24.9352 34 17 34C9.06475 34 5.09713 34 2.60034 31.5739C2.54142 31.5167 2.48331 31.4586 2.42605 31.3997C0 28.9029 0 24.9352 0 17Z" fill="#0098EA"/>
                            <mask id="mask1_402_2" style="mask-type:alpha" maskUnits="userSpaceOnUse" x="0" y="0" width="34" height="34">
                                <rect width="34" height="34" fill="#D9D9D9"/>
                            </mask>
                            <g mask="url(#mask1_402_2)">
                                <rect width="34" height="34" fill="black"/>
                                <mask id="mask2_402_2" style="mask-type:alpha" maskUnits="userSpaceOnUse" x="-16" y="-14" width="58" height="58">
                                    <circle cx="12.7618" cy="15.1819" r="28.4042" fill="#D9D9D9"/>
                                </mask>
                                <g mask="url(#mask2_402_2)">
                                    <g filter="url(#filter0_i_402_2)">
                                        <circle cx="12.7618" cy="15.1819" r="28.4042" fill="#F4A84A"/>
                                    </g>
                                    <mask id="mask3_402_2" style="mask-type:alpha" maskUnits="userSpaceOnUse" x="-7" y="-7" width="48" height="48">
                                        <circle cx="16.9939" cy="16.9076" r="23.8406" fill="#F94DA7"/>
                                    </mask>
                                    <g mask="url(#mask3_402_2)">
                                        <g filter="url(#filter1_i_402_2)">
                                            <circle cx="16.9939" cy="16.9076" r="23.8406" fill="#4D74FF"/>
                                        </g>
                                        <mask id="mask4_402_2" style="mask-type:alpha" maskUnits="userSpaceOnUse" x="-1" y="1" width="41" height="41">
                                            <ellipse cx="19.65" cy="21.6871" rx="19.9775" ry="20.1133" transform="rotate(126.608 19.65 21.6871)" fill="#AA01A1"/>
                                        </mask>
                                        <g mask="url(#mask4_402_2)">
                                            <g filter="url(#filter2_i_402_2)">
                                                <ellipse cx="19.65" cy="21.6871" rx="19.9775" ry="20.1133" transform="rotate(126.608 19.65 21.6871)" fill="#2152FD"/>
                                            </g>
                                            <mask id="mask5_402_2" style="mask-type:alpha" maskUnits="userSpaceOnUse" x="3" y="11" width="33" height="32">
                                                <ellipse cx="19.7479" cy="27.1363" rx="16.0511" ry="15.8151" fill="#6B00D1"/>
                                            </mask>
                                            <g mask="url(#mask5_402_2)">
                                                <g filter="url(#filter3_i_402_2)">
                                                    <ellipse cx="19.7479" cy="27.1363" rx="16.0511" ry="15.8151" fill="#0036F0"/>
                                                </g>
                                                <mask id="mask6_402_2" style="mask-type:alpha" maskUnits="userSpaceOnUse" x="3" y="20" width="24" height="23">
                                                    <ellipse cx="15.2689" cy="31.3355" rx="11.4141" ry="11.0544" transform="rotate(38.7507 15.2689 31.3355)" fill="#3012A0"/>
                                                </mask>
                                                <g mask="url(#mask6_402_2)">
                                                    <g filter="url(#filter4_i_402_2)">
                                                        <ellipse cx="15.2689" cy="31.3355" rx="11.4141" ry="11.0544" transform="rotate(38.7507 15.2689 31.3355)" fill="#0D30A8"/>
                                                    </g>
                                                    <g filter="url(#filter5_i_402_2)">
                                                        <ellipse cx="9.93273" cy="35.5261" rx="7.67031" ry="8.49117" transform="rotate(38.7507 9.93273 35.5261)" fill="#261969"/>
                                                    </g>
                                                </g>
                                            </g>
                                        </g>
                                    </g>
                                </g>
                            </g>
                        </g>
                        <defs>
                            <filter id="filter0_i_402_2" x="-15.6423" y="-13.2222" width="56.8083" height="60.8083" filterUnits="userSpaceOnUse" color-interpolation-filters="sRGB">
                                <feFlood flood-opacity="0" result="BackgroundImageFix"/>
                                <feBlend mode="normal" in="SourceGraphic" in2="BackgroundImageFix" result="shape"/>
                                <feColorMatrix in="SourceAlpha" type="matrix" values="0 0 0 0 0 0 0 0 0 0 0 0 0 0 0 0 0 0 127 0" result="hardAlpha"/>
                                <feOffset dy="4"/>
                                <feGaussianBlur stdDeviation="15"/>
                                <feComposite in2="hardAlpha" operator="arithmetic" k2="-1" k3="1"/>
                                <feColorMatrix type="matrix" values="0 0 0 0 0.925 0 0 0 0 0.51105 0 0 0 0 0 0 0 0 1 0"/>
                                <feBlend mode="normal" in2="shape" result="effect1_innerShadow_402_2"/>
                            </filter>
                            <filter id="filter1_i_402_2" x="-6.84668" y="-6.93301" width="47.6813" height="51.6812" filterUnits="userSpaceOnUse" color-interpolation-filters="sRGB">
                                <feFlood flood-opacity="0" result="BackgroundImageFix"/>
                                <feBlend mode="normal" in="SourceGraphic" in2="BackgroundImageFix" result="shape"/>
                                <feColorMatrix in="SourceAlpha" type="matrix" values="0 0 0 0 0 0 0 0 0 0 0 0 0 0 0 0 0 0 127 0" result="hardAlpha"/>
                                <feOffset dy="4"/>
                                <feGaussianBlur stdDeviation="10"/>
                                <feComposite in2="hardAlpha" operator="arithmetic" k2="-1" k3="1"/>
                                <feColorMatrix type="matrix" values="0 0 0 0 0.118148 0 0 0 0 0.311363 0 0 0 0 1 0 0 0 1 0"/>
                                <feBlend mode="normal" in2="shape" result="effect1_innerShadow_402_2"/>
                            </filter>
                            <filter id="filter2_i_402_2" x="-0.416382" y="1.65999" width="40.1327" height="44.0541" filterUnits="userSpaceOnUse" color-interpolation-filters="sRGB">
                                <feFlood flood-opacity="0" result="BackgroundImageFix"/>
                                <feBlend mode="normal" in="SourceGraphic" in2="BackgroundImageFix" result="shape"/>
                                <feColorMatrix in="SourceAlpha" type="matrix" values="0 0 0 0 0 0 0 0 0 0 0 0 0 0 0 0 0 0 127 0" result="hardAlpha"/>
                                <feOffset dy="4"/>
                                <feGaussianBlur stdDeviation="10"/>
                                <feComposite in2="hardAlpha" operator="arithmetic" k2="-1" k3="1"/>
                                <feColorMatrix type="matrix" values="0 0 0 0 0 0 0 0 0 0.158352 0 0 0 0 0.71097 0 0 0 1 0"/>
                                <feBlend mode="normal" in2="shape" result="effect1_innerShadow_402_2"/>
                            </filter>
                            <filter id="filter3_i_402_2" x="3.69678" y="11.3212" width="32.1022" height="35.6301" filterUnits="userSpaceOnUse" color-interpolation-filters="sRGB">
                                <feFlood flood-opacity="0" result="BackgroundImageFix"/>
                                <feBlend mode="normal" in="SourceGraphic" in2="BackgroundImageFix" result="shape"/>
                                <feColorMatrix in="SourceAlpha" type="matrix" values="0 0 0 0 0 0 0 0 0 0 0 0 0 0 0 0 0 0 127 0" result="hardAlpha"/>
                                <feOffset dy="4"/>
                                <feGaussianBlur stdDeviation="10"/>
                                <feComposite in2="hardAlpha" operator="arithmetic" k2="-1" k3="1"/>
                                <feColorMatrix type="matrix" values="0 0 0 0 0 0 0 0 0 0.15377 0 0 0 0 0.690397 0 0 0 1 0"/>
                                <feBlend mode="normal" in2="shape" result="effect1_innerShadow_402_2"/>
                            </filter>
                            <filter id="filter4_i_402_2" x="3.9939" y="20.1385" width="22.55" height="26.394" filterUnits="userSpaceOnUse" color-interpolation-filters="sRGB">
                                <feFlood flood-opacity="0" result="BackgroundImageFix"/>
                                <feBlend mode="normal" in="SourceGraphic" in2="BackgroundImageFix" result="shape"/>
                                <feColorMatrix in="SourceAlpha" type="matrix" values="0 0 0 0 0 0 0 0 0 0 0 0 0 0 0 0 0 0 127 0" result="hardAlpha"/>
                                <feOffset dy="4"/>
                                <feGaussianBlur stdDeviation="10"/>
                                <feComposite in2="hardAlpha" operator="arithmetic" k2="-1" k3="1"/>
                                <feColorMatrix type="matrix" values="0 0 0 0 0.107143 0 0 0 0 0 0 0 0 0 0.5 0 0 0 1 0"/>
                                <feBlend mode="normal" in2="shape" result="effect1_innerShadow_402_2"/>
                            </filter>
                            <filter id="filter5_i_402_2" x="1.93066" y="27.3461" width="16.0042" height="20.3599" filterUnits="userSpaceOnUse" color-interpolation-filters="sRGB">
                                <feFlood flood-opacity="0" result="BackgroundImageFix"/>
                                <feBlend mode="normal" in="SourceGraphic" in2="BackgroundImageFix" result="shape"/>
                                <feColorMatrix in="SourceAlpha" type="matrix" values="0 0 0 0 0 0 0 0 0 0 0 0 0 0 0 0 0 0 127 0" result="hardAlpha"/>
                                <feOffset dy="4"/>
                                <feGaussianBlur stdDeviation="10"/>
                                <feComposite in2="hardAlpha" operator="arithmetic" k2="-1" k3="1"/>
                                <feColorMatrix type="matrix" values="0 0 0 0 0.0236979 0 0 0 0 0 0 0 0 0 0.145833 0 0 0 1 0"/>
                                <feBlend mode="normal" in2="shape" result="effect1_innerShadow_402_2"/>
                            </filter>
                            <linearGradient id="paint0_linear_402_2" x1="35.3145" y1="30.2666" x2="-0.933158" y2="1.80859e-06" gradientUnits="userSpaceOnUse">
                                <stop/>
                                <stop offset="1"/>
                            </linearGradient>
                        </defs>
                    </svg>
                </div>
            </v-toolbar>
            <v-divider></v-divider>
            <div
                class="d-flex justify-center align-center align-self-center flex-column"
                style="min-height: calc(100vh - 65px);"
            >
                <?= $this->renderSection('content') ?>
            </div>
        </v-main>
    </v-layout>
    <v-snackbar v-model="error.status">
        {{ error.message }}
        <template v-slot:actions>
            <v-btn variant="text" color="pink" @click="error.status = false">
                OK
            </v-btn>
        </template>
    </v-snackbar>
</div>
</body>
<script>
    const { createApp } = Vue
    const { createVuetify } = Vuetify

    const vuetify = createVuetify()

    const app = createApp({
        data() {
            return {
                error: {
                    status: false,
                    message: ""
                },
                base_url: "",
                database_name: "",
                database_hostname: "",
                database_user: "",
                database_password: "",
                database_port: "3306",
                database_loading: false,
                git_username: "",
                git_token: "",
                git_loading: false,
                admin_email: "",
                admin_password: "",
                admin_re_password: "",
                admin_loading: false
            }
        },
        methods: {
            createAdmin() {
                this.admin_loading = true;
                axios.post(`/install/create_admin`, {
                    email: this.admin_email,
                    password: this.admin_password,
                    re_password: this.admin_re_password
                }).then(() => {
                    this.admin_loading = false;
                    window.location.href = '4';
                }).catch(e => {
                    this.admin_loading = false;
                    const message = this.getMessage(e);
                    this.error = {
                        status: true,
                        message: message
                    }
                });
            },
            createDatabaseConnection() {
                this.database_loading = true;
                axios.post(`/install/connect_db`, {
                    url: this.base_url,
                    name: this.database_name,
                    hostname: this.database_hostname,
                    username: this.database_user,
                    password: this.database_password,
                    port: this.database_port
                }).then(() => {
                    this.database_loading = false;
                    window.location.href = '2';
                }).catch(e => {
                    this.database_loading = false;
                    const message = this.getMessage(e);
                    this.error = {
                        status: true,
                        message: message
                    }
                });
            },
            createGitConnection() {
                this.git_loading = true;
                axios.post(`/install/connect_git`, {
                    git_username: this.git_username,
                    git_token: this.git_token
                }).then(() => {
                    this.git_loading = false;
                    window.location.href = '3';
                }).catch(e => {
                    this.git_loading = false;
                    const message = this.getMessage(e);
                    this.error = {
                        status: true,
                        message: message
                    }
                });
            },
            getMessage(val) {
                if (typeof val === 'string') {
                    return val;
                }
                if (val.response) {
                    let obj = val.response.data.message;
                    if (typeof obj === "object") {
                        let text = "";
                        Object.keys(obj).forEach(function(key) {
                            text += obj[key] + "\n";
                        });
                        return text;
                    }
                    return val.response.data.message;
                }
                return "?????"
            }
        },
        beforeMount() {
            console.log('Component is about to be mounted')
        }
    })

    app.use(vuetify).mount('#app')
</script>
</html>