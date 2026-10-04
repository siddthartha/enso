# MCP server for Vultr (https://github.com/rsp2k/mcp-vultr), packaged for the local
# agent sandbox. Runs FastMCP over streamable HTTP so the agent container can reach
# it by name on the shared `mcp` Docker network:  http://mcp-vultr:8080/mcp
#
# Run:    docker compose up -d mcp-vultr          (key: VULTR_API_KEY in the root .env)
# Prod:   docker-compose.prod.yml puts it behind the "mcp" profile, so it is not started.
# stdio:  docker run -i --rm -e VULTR_API_KEY=... mcp-vultr:latest mcp-vultr

FROM python:3.12-slim

ENV PYTHONUNBUFFERED=1 \
    PIP_NO_CACHE_DIR=1 \
    PIP_DISABLE_PIP_VERSION_CHECK=1 \
    MCP_HOST=0.0.0.0 \
    MCP_PORT=8080 \
    MCP_PATH=/mcp

# fastmcp<3 is load-bearing: mcp-vultr mounts sub-servers with `prefix=`,
# which FastMCP 4.x renamed. Pin it alongside the package.
RUN pip install --upgrade pip \
 && pip install "mcp-vultr" "fastmcp>=2.11,<3" \
 && adduser --disabled-password --gecos "" --uid 1000 mcp

COPY --chmod=0755 serve.py /usr/local/bin/mcp-vultr-http

USER mcp
WORKDIR /home/mcp

EXPOSE 8080

HEALTHCHECK --interval=30s --timeout=5s --start-period=20s --retries=3 \
  CMD python -c "import socket,os; s=socket.create_connection(('127.0.0.1', int(os.environ.get('MCP_PORT','8080'))), 3); s.close()" || exit 1

CMD ["mcp-vultr-http"]
