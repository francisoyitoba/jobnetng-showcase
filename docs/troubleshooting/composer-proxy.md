# Composer install fails with `CONNECT tunnel failed, response 403`

If `composer install` stops with a message similar to:

```
curl error 56 while downloading https://repo.packagist.org/packages.json:
CONNECT tunnel failed, response 403
```

it means Composer could not create an HTTPS tunnel through the configured proxy.
This usually happens when:

1. An outbound proxy blocks unauthenticated requests; or
2. A stale `HTTP(S)_PROXY` environment variable points to a proxy that is no
   longer reachable.

## Fixes

1. **Check your proxy environment.**
   ```bash
   env | grep -i _proxy
   ```
   Remove any unexpected proxy variables or export the correct credentials:
   ```bash
   # Remove a wrong proxy
   unset HTTP_PROXY HTTPS_PROXY

   # …or configure the authenticated proxy
   export HTTPS_PROXY="http://user:pass@proxy.example.com:8080"
   ```

2. **Tell Composer which proxy to use.** If you need a proxy, persist it in
   Composer's config so every run knows about it:
   ```bash
   composer config -g --unset proxy
   composer config -g --unset http-basic.repo.packagist.org
   composer config -g --global proxy http://user:pass@proxy.example.com:8080
   ```

3. **Bypass the proxy when possible.** When developing locally without an
   outbound proxy, disable Composer's proxy settings entirely:
   ```bash
   composer config -g --unset proxy
   composer config -g --unset http-basic.repo.packagist.org
   ```

After adjusting the proxy settings, re-run:

```bash
composer clear-cache
composer install
```

Composer should now be able to reach `https://repo.packagist.org` and download
packages normally.
