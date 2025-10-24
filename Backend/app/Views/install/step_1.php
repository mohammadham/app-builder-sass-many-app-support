<?= $this->extend('layout') ?>

<?= $this->section('content') ?>
<div style="width: 420px">
    <div class="text-h5 mb-5 text-center">
        1/4. <?php echo lang("Install.install_12"); ?>
    </div>
    <div class="border rounded pa-4" style="width: 420px">
        <v-text-field
                label="<?php echo lang("Install.install_29"); ?>"
                variant="outlined"
                density="comfortable"
                placeholder="https://site.com/"
                v-model="base_url"
        ></v-text-field>
        <v-text-field
                label="<?php echo lang("Install.install_4"); ?>"
                variant="outlined"
                density="comfortable"
                placeholder="database_name"
                v-model="database_name"
        ></v-text-field>
        <v-text-field
                label="<?php echo lang("Install.install_8"); ?>"
                variant="outlined"
                density="comfortable"
                placeholder="3306"
                v-model="database_port"
        ></v-text-field>
        <v-text-field
                label="<?php echo lang("Install.install_5"); ?>"
                variant="outlined"
                density="comfortable"
                placeholder="localhost"
                v-model="database_hostname"
        ></v-text-field>
        <v-text-field
                label="<?php echo lang("Install.install_6"); ?>"
                variant="outlined"
                density="comfortable"
                placeholder="root"
                autocomplete="off"
                v-model="database_user"
        ></v-text-field>
        <v-text-field
                label="<?php echo lang("Install.install_7"); ?>"
                variant="outlined"
                density="comfortable"
                placeholder="************"
                autocomplete="off"
                type="password"
                v-model="database_password"
        ></v-text-field>
        <v-btn variant="flat" :loading="database_loading" block color="#184BFC" size="large" @click="createDatabaseConnection">
            <?php echo lang("Install.install_10"); ?>
        </v-btn>
    </div>
    <div class="mt-3 text-center text-caption">
        © 2024 Flangapp PRO. All rights reserved. Regular license.
    </div>
</div>
<?= $this->endSection() ?>
