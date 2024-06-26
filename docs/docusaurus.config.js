// @ts-check
// Note: type annotations allow type checking and IDEs autocompletion

import {themes} from "prism-react-renderer";

/** @type {import('@docusaurus/types').Config} */
const config = {
  title: 'Plates Template Engine',
  tagline: 'Plates, the native PHP template system that\'s fast, easy to use and easy to extend.',
  favicon: 'img/favicon.svg',
  headTags: [
    {
      tagName: 'link',
      attributes: {
        rel: 'mask-icon',
        color: '#19324c',
        href: '/img/safari-pinned-tab.svg',
      },
    },
    {
      tagName: 'link',
      attributes: {
        rel: 'apple-touch-icon',
        sizes: '180x180',
        href: '/img/apple-touch-icon.png',
      },
    },
    {
      tagName: 'link',
      attributes: {
        rel: 'icon',
        type: 'image/png',
        href: '/img/favicon-light.png',
      },
    },
    {
      tagName: 'link',
      attributes: {
        rel: 'icon',
        type: 'image/png',
        href: '/img/favicon-dark.png',
        media: '(prefers-color-scheme: dark)',
      },
    },
    {
      tagName: 'meta',
      attributes: {
        name: 'theme-color',
        content: '#19324c',
      },
    },
  ],

  // Set the production url of your site here
  url: 'https://plates.jinya.dev',
  // Set the /<baseUrl>/ pathname under which your site is served
  // For GitHub pages deployment, it is often '/<projectName>/'
  baseUrl: '/',

  // GitHub pages deployment config.
  // If you aren't using GitHub pages, you don't need these.
  organizationName: 'jinya-cms', // Usually your GitHub org/user name.
  projectName: 'plates', // Usually your repo name.

  onBrokenLinks: 'throw',
  onBrokenMarkdownLinks: 'warn',

  // Even if you don't use internalization, you can use this field to set useful
  // metadata like html lang. For example, if your site is Chinese, you may want
  // to replace "en" with "zh-Hans".
  i18n: {
    defaultLocale: 'en',
    locales: ['en'],
  },

  presets: [
    [
      'classic',
      /** @type {import('@docusaurus/preset-classic').Options} */
      ({
        docs: {
          sidebarPath: require.resolve('./sidebars.js'),
        },
        theme: {
          customCss: require.resolve('./src/css/custom.css'),
        },
      }),
    ],
  ],

  themeConfig:
  /** @type {import('@docusaurus/preset-classic').ThemeConfig} */
    ({
      // Replace with your project's social card
      image: 'img/favicon-light.png',
      navbar: {
        title: 'Plates',
        logo: {
          alt: 'Plates',
          src: 'img/favicon.svg',
        },
        items: [
          {
            to: 'docs/intro',
            activeBasePath: 'docs',
            label: 'Docs',
            position: 'left',
          },
          {
            href: 'https://gitlab.imanuel.dev/jinya-cms/plates/',
            label: 'GitLab',
            position: 'right',
          },
          {
            href: 'https://github.com/jinya-cms/plates/',
            label: 'Github',
            position: 'right',
          },
        ],
      },
      footer: {
        style: 'dark',
        copyright: `Copyright © ${new Date().getFullYear()} Jinya Developers. Built with Docusaurus.`,
      },
      prism: {
        theme: themes.github,
        darkTheme: themes.dracula,
        additionalLanguages: ['php', 'bash'],
      },
    }),
};

module.exports = config;
