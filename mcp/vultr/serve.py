#!/usr/local/bin/python
"""Serve mcp-vultr over streamable HTTP, bound to all interfaces.

The package's own `mcp-vultr` entry point defaults to the stdio transport and,
for HTTP, to FastMCP's 127.0.0.1 bind — neither reachable from another container.
This wrapper keeps the same server (no OAuth) and only changes the transport.
"""

import os
import sys

from mcp_vultr._version import __version__
from mcp_vultr.fastmcp_server import create_vultr_mcp_server


def main() -> None:
    if not os.environ.get("VULTR_API_KEY"):
        # Idle instead of exiting: under `restart: unless-stopped` an exit would become a
        # restart loop. The port stays closed, so the HEALTHCHECK reports "unhealthy".
        print(
            "VULTR_API_KEY is empty: set it in the root .env and `docker compose up -d mcp-vultr`. Idling.",
            file=sys.stderr,
        )
        import signal
        signal.pause()
        return

    host = os.environ.get("MCP_HOST", "0.0.0.0")
    port = int(os.environ.get("MCP_PORT", "8080"))
    path = os.environ.get("MCP_PATH", "/mcp")

    print(f"mcp-vultr v{__version__}: streamable HTTP on http://{host}:{port}{path}", file=sys.stderr)

    server = create_vultr_mcp_server()
    server.run(transport="http", host=host, port=port, path=path)


if __name__ == "__main__":
    main()
