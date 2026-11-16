<?php

return [

    /*
    | Disabled by default. While false, /mcp returns 404 and does not
    | authenticate, query business data, or write MCP audit rows.
    */
    'enabled' => filter_var(env('MCP_ENABLED', false), FILTER_VALIDATE_BOOLEAN),

    'rate_limit_per_minute' => (int) env('MCP_RATE_LIMIT', 60),

    'auth_failures_per_minute' => (int) env('MCP_AUTH_RATE_LIMIT', 20),

    'token_ttl_days' => (int) env('MCP_TOKEN_TTL_DAYS', 90),

    'protocol_version' => '2025-03-26',

    'server_name' => 'mv-miami-rental',

    'server_version' => '1.0.0',

];
