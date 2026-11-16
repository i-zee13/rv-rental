export type McpClientConfig = {
  endpoint: string;
  token: string;
  workspaceId?: string;
};

type JsonRpcSuccess = {
  jsonrpc: "2.0";
  id: number | string | null;
  result?: unknown;
  error?: { code: number; message: string };
};

export type McpToolResult<T> = {
  ok: boolean;
  data?: T;
  error?: string;
};

let nextId = 1;

export function createMvMcpClient(config: McpClientConfig) {
  if (!config.endpoint || !config.token) {
    throw new Error("MV MCP endpoint and token are required.");
  }

  async function request(method: string, params?: Record<string, unknown>): Promise<unknown> {
    const headers: Record<string, string> = {
      Authorization: `Bearer ${config.token}`,
      Accept: "application/json, text/event-stream",
      "Content-Type": "application/json",
    };

    if (config.workspaceId) {
      headers["X-Wwt-Workspace"] = config.workspaceId;
    }

    const response = await fetch(config.endpoint, {
      method: "POST",
      headers,
      body: JSON.stringify({
        jsonrpc: "2.0",
        id: nextId++,
        method,
        params: params ?? {},
      }),
    });

    if (response.status === 202) {
      return null;
    }

    const body = (await response.json()) as JsonRpcSuccess;

    if (!response.ok || body.error) {
      throw new Error(body.error?.message || `MCP request failed (${response.status}).`);
    }

    return body.result;
  }

  return {
    async initialize() {
      const initialized = await request("initialize", {
        protocolVersion: "2025-03-26",
        capabilities: {},
        clientInfo: { name: "aide", version: "1.0.0" },
      });

      const notifyHeaders: Record<string, string> = {
        Authorization: `Bearer ${config.token}`,
        Accept: "application/json, text/event-stream",
        "Content-Type": "application/json",
      };

      if (config.workspaceId) {
        notifyHeaders["X-Wwt-Workspace"] = config.workspaceId;
      }

      await fetch(config.endpoint, {
        method: "POST",
        headers: notifyHeaders,
        body: JSON.stringify({ jsonrpc: "2.0", method: "notifications/initialized" }),
      });

      return initialized;
    },

    listTools() {
      return request("tools/list");
    },

    async callTool<T = Record<string, unknown>>(
      name: string,
      args: Record<string, unknown> = {},
    ): Promise<McpToolResult<T>> {
      const result = (await request("tools/call", { name, arguments: args })) as {
        isError?: boolean;
        structuredContent?: T & { error?: string };
        content?: Array<{ text?: string }>;
      };

      if (result?.isError) {
        return {
          ok: false,
          error: result.structuredContent?.error || result.content?.[0]?.text || "Tool failed.",
        };
      }

      return { ok: true, data: result.structuredContent };
    },
  };
}

export async function example(config: McpClientConfig) {
  const client = createMvMcpClient(config);
  await client.initialize();
  await client.listTools();

  return client.callTool("mv_get_business_overview", {});
}
