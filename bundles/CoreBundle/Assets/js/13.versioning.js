Autoborna.contentVersions = {};
Autoborna.versionNamespace = '';
Autoborna.currentContentVersion = -1;

/**
 * Setup versioning for the given namespace
 *
 * @param undoCallback function
 * @param redoCallback function
 * @param namespace
 */
Autoborna.prepareVersioning = function (undoCallback, redoCallback, namespace) {
    // Check if localStorage is supported and if not, disable undo/redo buttons
    if (!Autoborna.isLocalStorageSupported()) {
        mQuery('.btn-undo').prop('disabled', true);
        mQuery('.btn-redo').prop('disabled', true);

        return;
    }

    mQuery('.btn-undo')
        .prop('disabled', false)
        .on('click', function() {
            Autoborna.undoVersion(undoCallback);
        });

    mQuery('.btn-redo')
        .prop('disabled', false)
        .on('click', function() {
            Autoborna.redoVersion(redoCallback);
        });

    Autoborna.currentContentVersion = -1;

    if (!namespace) {
        namespace = window.location.href;
    }

    if (typeof Autoborna.contentVersions[namespace] == 'undefined') {
        Autoborna.contentVersions[namespace] = [];
    }

    Autoborna.versionNamespace = namespace;

    console.log(namespace);
};

/**
 * Clear versioning
 *
 * @param namespace
 */
Autoborna.clearVersioning = function () {
    if (!Autoborna.versionNamespace) {
        throw 'Versioning not configured';
    }

    if (typeof Autoborna.contentVersions[Autoborna.versionNamespace] !== 'undefined') {
        delete Autoborna.contentVersions[Autoborna.versionNamespace];
    }

    Autoborna.versionNamespace = '';
    Autoborna.currentContentVersion = -1;
};

/**
 * Store a version
 *
 * @param content
 */
Autoborna.storeVersion = function(content) {
    if (!Autoborna.versionNamespace) {
        throw 'Versioning not configured';
    }

    // Store the content
    Autoborna.contentVersions[Autoborna.versionNamespace].push(content);

    // Set the current location to the latest spot
    Autoborna.currentContentVersion = Autoborna.contentVersions[Autoborna.versionNamespace].length;
};

/**
 * Decrement a version
 *
 * @param callback
 */
Autoborna.undoVersion = function(callback) {
    console.log('undo');
    if (!Autoborna.versionNamespace) {
        throw 'Versioning not configured';
    }

    if (Autoborna.currentContentVersion < 0) {
        // Nothing to undo

        return;
    }

    var version = Autoborna.currentContentVersion - 1;
    if (Autoborna.getVersion(version, callback)) {
        --Autoborna.currentContentVersion;
    };
};

/**
 * Increment a version
 *
 * @param callback
 */
Autoborna.redoVersion = function(callback) {
    console.log('redo');
    if (!Autoborna.versionNamespace) {
        throw 'Versioning not configured';
    }

    if (Autoborna.currentContentVersion < 0 || Autoborna.contentVersions[Autoborna.versionNamespace].length === Autoborna.currentContentVersion) {
        // Nothing to redo

        return;
    }

    var version = Autoborna.currentContentVersion + 1;
    if (Autoborna.getVersion(version, callback)) {
        ++Autoborna.currentContentVersion;
    };
};

/**
 * Check for a given version and execute callback
 *
 * @param version
 * @param command
 * @returns {boolean}
 */
Autoborna.getVersion = function(version, callback) {
    var content = false;
    if (typeof Autoborna.contentVersions[Autoborna.versionNamespace][version] !== 'undefined') {
        content = Autoborna.contentVersions[Autoborna.versionNamespace][version];
    }

    if (false !== content && typeof callback == 'function') {
        callback(content);

        return true;
    }

    return false;
};