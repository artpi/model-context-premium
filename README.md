# Model Context Premium

**Get paid for your wisdom!**

Model Context Premium is a WordPress plugin designed to integrate with WooCommerce sites, enabling a Model Context Protocol (MCP) server. This server allows AI clients, such as Cursor, to interact with premium content hosted on the WooCommerce site.
The core functionality includes facilitating a checkout flow initiated from the MCP client for users to purchase access to premium content, and providing access to this content, which is stored as a vectorized database (e.g., in an OpenAI vector store).
The plugin aims to provide a novel way for content creators, especially researchers, to monetize their raw or precompiled data by allowing AI models to access and utilize this information without releasing the entire dataset publicly.

[Video demo here](https://x.com/artpi/status/1920110096920572202)

More in [PRD](./docs/prd.md)

## How this works

The plugin uses [WordPress-MCP from Automattic to expose an MCP server.](https://github.com/Automattic/wordpress-mcp). We introduce a new tool to that server that is paywalled.
- If a customer has purchased the product, than we het him through and complete the call
- If they didnt pay, they get payment link.

### What are you selling

In this case, we are selling access to a RAG search service to retrieve information from a body of work. The idea is that you would want to monetize your datasets but not share them everywhere so people can copy them. This way, people can use their AIs to consult your custom data without owning it.


## Setup

### Prerequisites

Since WordPress does not have a native RAG service (yet), we use OpenAI Vector stores. You will need
- OpenAI token
- You need to create a vector store in your account
- Upload some files there (upload from WP-Admin is not yet implemented)
- You need to note this vector store id.

### Running this locally

```
nvm use
npm install
npm run wp-env start
```

- Access localhost:8901/wp-admin
- Turn on permalinks
- Set up a product
- Create a seperate customer account with admin role (I know, this is a bug in wordpress-mcp)
- Create an applicatio password for this customer
- Change hardcoded vector store id and product id in [here](./tools/McpSearchPrivateData.php)
- Change data in [MCP settings](./.cursor/mcp.json)
- Make sure you have this installed `npx -y @automattic/mcp-wordpress-remote`
- See if MCP server correctly set up in cursor
- now you should be able to ask for paid tool access

## TODO

- Streamline auth and account creation using JWT
- Fix upstream issues in wordpress-mcp
- Introduced new product type

