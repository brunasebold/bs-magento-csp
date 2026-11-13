# Security

Report a vulnerability in this module by email to sebold.bruna@gmail.com. Please do not open a public issue for a security problem.

This module adds hosts to a Content-Security-Policy header. It does not grant `'unsafe-inline'` or `'unsafe-eval'`. A host saved here is still a host the browser will trust for that directive, so treat admin access to **Stores > CSP Whitelist** as a security permission.
