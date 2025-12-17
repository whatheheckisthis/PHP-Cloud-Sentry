# PHP-Cloud-Sentry
Real-Time Cryptographic Threat Intelligence for High-Density PHP Cloud Architectures
This documentation provides a comprehensive overview of the **PHP Cloud Sentry** suite, focusing on its newly derived **Triple-Proxy Architecture** and integration with **LangChain**, **Azure Data Lakes**, and **Kafka**.

---


## Project Overview

PHP Cloud Sentry is a professional-grade cybersecurity framework engineered to identify and mitigate suspicious cryptocurrency-related activities within high-density PHP cloud environments. The system provides real-time cryptographic threat detection by integrating an asynchronous microservice with a custom "DIY AWS Lambda" hosting environment.

By employing a proprietary Triple-Proxy routing pattern and mathematically sound threat variance modeling, the suite secures enterprise-scale deployments of Drupal, WordPress, and custom PHP applications without compromising system throughput.

---

## Core Capabilities

### Asynchronous Intelligence Engine

* **Static Code Analysis:** Automated inspection of source code to detect unauthorized encryption algorithms and suspicious API call patterns.
* **Crypto Spec 3.0 Compliance:** Full adherence to the Crypto JSON Output Specification Version 3.0, utilizing deterministic SHA1-concatenation for package verification.
* **Forensic Grouping:** Evidence is logically deduplicated by File SHA1, optimizing report readability and forensic analysis for large application clusters.

### Advanced Intelligence Integrations

* **LangChain Reasoning Layer:** Employs LangChain agents to provide "Chain-of-Thought" justifications for detected threats, transforming the system into an autonomous security expert.
* **Real-Time Data Plane:** Utilizes **Apache Kafka** for live event streaming and **Azure Data Lake Storage (ADLS Gen2)** for long-term persistence and Retrieval-Augmented Generation (RAG).
* **Enterprise Application Layer:** Optimized for high-density **Drupal** and **Apache PHP** deployments, utilizing a multi-layered reverse proxy chain to isolate security logic from public traffic.

---

## Technical Architecture

The framework separates the application hosting layer from the security logic layer to maintain peak performance and defensive depth.

### Triple-Proxy Routing Model

| Layer | Service | Responsibility | Technical Stack |
| --- | --- | --- | --- |
| **Layer 1** | Edge Proxy | SSL Termination and Public Request Management | Apache 2.4 |
| **Layer 2** | Internal Proxy | High-Concurrency Load Balancing and Request Filtering | Nginx |
| **Layer 3** | Analysis API | Asynchronous Intelligence, LangChain Agents, and Forensics | FastAPI (Python) |

---

## Getting Started

### Prerequisites

* **AWS Environment:** Configured with EFS (mounted at `/srv`) and RDS instances.
* **Container Engine:** Docker and Docker Compose v3.8+.
* **Python Stack:** Python 3.10+ for local intelligence modules and FastAPI backend.

### Installation and Initialization

1. **Clone the Repository:**
```bash
git clone https://github.com/whatheheckisthis/php-cloud-sentry.git
cd php-cloud-sentry

```


2. **Environment Configuration:**
Create a `.env` file in the root directory and define your cloud endpoints and API keys. Ensure this file is listed in your `.gitignore`.
```text
AWS_EFS_ID=fs-xxxxxxxx
RDS_ENDPOINT=your-db.cluster.aws.com
KAFKA_BOOTSTRAP_SERVERS=your-kafka:9092
AZURE_STORAGE_ACCOUNT_URL=https://youraccount.blob.core.windows.net

```


3. **Build the Unified Security Environment:**
```bash
docker build -t php-cloud-sentry-suite .

```


4. **Launch the Triple-Proxy Stack:**
```bash
docker-compose up -d

```



---

## Synthetic Data and Monitoring

The suite includes an ETL orchestration layer designed for Databricks and Spark SQL, enabling advanced threat modeling without exposing sensitive PII.

* **Variance Modeling:** Mathematical modeling using Gaussian distributions and Directed Acyclic Graphs (DAG) to simulate realistic transaction signatures.
* **Collaborative Dashboards:** Security scores are mounted directly into **Google Sheets** via the Databricks Connector, facilitating real-time human review and triage.

---

## License

Copyright (c) 2025 whatheheckisthis.

Licensed under the Apache License, Version 2.0 (the "License"); you may not use this file except in compliance with the License. You may obtain a copy of the License at:

[http://www.apache.org/licenses/LICENSE-2.0](http://www.apache.org/licenses/LICENSE-2.0)

Unless required by applicable law or agreed to in writing, software distributed under the License is distributed on an "AS IS" BASIS, WITHOUT WARRANTIES OR CONDITIONS OF ANY KIND, either express or implied. See the License for the specific language governing permissions and limitations under the License.
