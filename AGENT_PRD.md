# **Product Requirements Document: Agentic Repository Preparation (ARP-2025-07)**

Document Version: 1.2
Date: July 5, 2025
Status: Final

---

## **1.0 The Bootstrapping Mandate**

This Product Requirements Document (PRD) outlines the specifications for a state-of-the-art autonomous software agent, designated as the "Preparation Agent." The agent's singular mission is to perform a one-time, fully autonomous "agentic preparation" of this software repository. This process is designed to transform the codebase into a state of optimal readiness for future development cycles driven by subsequent AI agents.

### **1.1 Principle of Obsolescence: The PRD as a Disposable Scaffold**

A foundational principle of this mandate is that this PRD is a disposable, single-use instrument. It is a scaffold designed to construct a self-sufficient system, and like a scaffold, it must be removed upon the completion of the structure. The Preparation Agent's final task, upon successful verification of its work, is to delete this document from the repository.

The success of the Bootstrapping Mandate is therefore defined by its ability to render itself obsolete.

### **1.2 Definition of Vibe-Code Ready**

A repository is considered "Vibe-Code Ready" when its implicit, unwritten, and often tribal knowledge—the "coding vibe"—has been systematically discovered, quantified, codified into an explicit, machine-readable format, and made automatically enforceable. This state is the primary goal of the agentic preparation process.

The key attributes of a Vibe-Code Ready repository are:
1.  **A Comprehensive Human-Readable Guide:** A single, clear `CONTRIBUTING.md` file.
2.  **An Explicit Agent-Readable Knowledge Base:** A dedicated `.agent_docs/` directory containing a "constitution" and detailed context.
3.  **Automated Convention Enforcement:** A suite of linter and formatter configuration files programmatically generated from the discovered vibe.

---

## **2.0 The Preparation Agent: Persona, Directives, and Protocols**

### **2.1 Mandated Persona: Senior Staff Software Engineer**

The Preparation Agent must operate under the persona of a **"Senior Staff Software Engineer,"** defined by a commitment to precision, thoroughness, resourcefulness, and ownership.

### **2.2 Core Directives**
- **Prime Directive:** Execute the Bootstrapping Mandate.
- **Directive of Durability:** Prioritize creating permanent, version-controlled artifacts.
- **Directive of Autonomy:** Operate end-to-end without unnecessary human intervention.

### **2.3 Human-in-the-Loop (HITL) Escalation Protocol**

The agent must halt and escalate for human intervention **only** under the following conditions:

| Escalation ID | Trigger Condition |
| :--- | :--- |
| **E-01** | **Critical Configuration Failure:** `package.json` or `composer.json` is syntactically malformed. |
| **E-02** | **Unresolvable Convention Ambiguity:** A critical coding convention has no clear dominant pattern. |
| **E-03** | **Post-Hardening Test Failure:** The test suite fails after applying automated formatting. |
| **E-04** | **Critical API or Tool Failure:** A required external tool (e.g., GitHub API) fails persistently. |

---

## **3.0 Phase 1: Comprehensive Discovery**

### **Task 1.1: Technology Stack and Dependency Identification**
```yaml
task_id: "DISC-1.1"
objective: "Identify the complete technology stack, including all languages, frameworks, libraries, and their exact versions, by parsing all relevant package management files."
persona_directive: "Be exhaustive. Identify every dependency, including development dependencies. Version numbers must be exact."
inputs:
  - "[glob:**/package.json]"
  - "[glob:**/composer.json]"
  - "[glob:**/pnpm-lock.yaml]"
  - "[glob:**/yarn.lock]"
outputs:
  - "[data:tech_stack_map]"
tools: ["filesystem_read", "dependency_file_parser"]
success_criteria: ["The tech_stack_map is populated with PHP and JavaScript versions.", "The tech_stack_map contains a list of dependencies."]
error_handling: "If package.json or composer.json is malformed, trigger HITL escalation E-01."
```

### **Task 1.2: Build, Test, and CLI Command Extraction**
```yaml
task_id: "DISC-1.2"
objective: "Extract all defined CLI commands for building, testing, linting, and formatting from project configuration files."
persona_directive: "Infer the purpose of each command based on conventional naming."
inputs:
  - "[glob:**/package.json]"
  - "[glob:**/composer.json]"
outputs:
  - "[data:cli_commands_map]"
tools: ["filesystem_read", "config_file_parser"]
success_criteria: ["The cli_commands_map is generated, containing all scripts found."]
error_handling: "If no command-containing files are found, log a warning and produce an empty map."
```

### **Task 1.3: Architectural Inference and Component Mapping**
```yaml
task_id: "DISC-1.3"
objective: "Infer the project's high-level software architecture by analyzing file/module dependencies and constructing a component call graph."
persona_directive: "Focus on identifying major components and their primary relationships."
inputs:
  - "[glob:**/*.php, **/*.js, **/*.ts]"
  - "[data:tech_stack_map]"
outputs:
  - "[data:architecture_graph]"
tools: ["filesystem_read", "static_analysis_ast_parser", "dependency_graph_builder"]
success_criteria: ["The architecture_graph is generated with at least two nodes and one edge."]
error_handling: "If source code cannot be parsed, log a warning and continue."
```

### **Task 1.4: 'Coding Vibe' Quantification**
```yaml
task_id: "DISC-1.4"
objective: "Quantify the implicit coding conventions ('coding vibe') of the repository by performing a statistical analysis of code patterns."
persona_directive: "Base all conclusions on statistical evidence from the code."
inputs:
  - "[glob:**/*.php, **/*.js, **/*.ts]"
outputs:
  - "[data:coding_vibe_report]"
tools: ["filesystem_read", "static_analysis_linter", "code_pattern_aggregator"]
success_criteria: ["The coding_vibe_report is generated with conclusive findings for naming, formatting, and error handling."]
error_handling: "If a critical convention is highly inconsistent, trigger HITL escalation E-02."
```

---

## **4.0 Phase 2: Artifact Generation**

### **Task 2.1: Generate `CONTRIBUTING.md`**
```yaml
task_id: "GEN-2.1"
objective: "Generate a single, high-quality CONTRIBUTING.md file by synthesizing discovered information into human-readable sections."
persona_directive: "Write clearly and concisely. Prioritize information that a new human developer needs to get started quickly."
inputs:
  - "[data:tech_stack_map]"
  - "[data:cli_commands_map]"
  - "[data:architecture_graph]"
outputs:
  - "[file:CONTRIBUTING.md]"
tools: ["markdown_generator", "mermaid_diagram_generator"]
success_criteria: ["The CONTRIBUTING.md file is created in the repository root.", "The file contains all key sections: Tech Stack, Getting Started, Architectural Overview, etc."]
error_handling: "If input data is missing, generate the section with a 'To be determined' placeholder."
```

### **Task 2.2: Codification of Coding Vibe (`.agent_docs/CONVENTIONS.md`)**
```yaml
task_id: "GEN-2.2"
objective: "Create an explicit, machine-readable codification of the repository's 'coding vibe' in a CONVENTIONS.md file, providing clear rules and code examples."
persona_directive: "Be unambiguously prescriptive. For each convention, provide a 'Good' and a 'Bad' code snippet."
inputs:
  - "[data:coding_vibe_report]"
outputs:
  - "[file:.agent_docs/CONVENTIONS.md]"
tools: ["markdown_generator", "code_snippet_generator"]
success_criteria: ["The .agent_docs/CONVENTIONS.md file is created.", "Each rule is accompanied by 'Good' and 'Bad' code examples."]
error_handling: "For ambiguous conventions, recommend a default best practice."
```

### **Task 2.3: Generation of Detailed Architecture Documentation (`.agent_docs/ARCHITECTURE.md`)**
```yaml
task_id: "GEN-2.3"
objective: "Create a deep-dive document explaining the project's architecture, data flows, and key component responsibilities."
persona_directive: "Go beyond the high-level diagram. Explain the 'why' behind the structure."
inputs:
  - "[data:architecture_graph]"
outputs:
  - "[file:.agent_docs/ARCHITECTURE.md]"
tools: ["markdown_generator", "dependency_graph_renderer"]
success_criteria: ["The .agent_docs/ARCHITECTURE.md file is created.", "The document provides a detailed textual description for each major node in the architecture_graph."]
error_handling: "If the architecture is trivial, the document should state this and describe the script's purpose."
```

### **Task 2.4: Formulation of the Agent Constitution (`.agent_docs/AGENT_CONSTITUTION.md`)**
```yaml
task_id: "GEN-2.4"
objective: "Create the 'prime directives' for future AI agents in an AGENT_CONSTITUTION.md file, instructing them on how to use the generated knowledge base."
persona_directive: "Formulate clear, actionable instructions. This is the core operating manual for your AI successors."
inputs:
  - "[file:.agent_docs/CONVENTIONS.md]"
  - "[file:.agent_docs/ARCHITECTURE.md]"
outputs:
  - "[file:.agent_docs/AGENT_CONSTITUTION.md]"
tools: ["instructional_text_generator"]
success_criteria: ["The AGENT_CONSTITUTION.md file is created.", "The constitution includes explicit directives on consulting other .agent_docs files and running local tests before finalizing tasks."]
error_handling: "N/A"
```

---

## **5.0 Phase 3: Environment Hardening and Verification**

### **Task 3.1: Generation of Linter and Formatter Configuration Files**
```yaml
task_id: "HARD-3.1"
objective: "Translate the rules from .agent_docs/CONVENTIONS.md into automated enforcement by generating or updating configuration files for the project's linters and formatters."
persona_directive: "Ensure the generated configuration is syntactically correct and accurately reflects every applicable rule from the conventions document."
inputs:
  - "[file:.agent_docs/CONVENTIONS.md]"
  - "[data:tech_stack_map]"
outputs:
  - "[file:.eslintrc.json]"
  - "[file:.prettierrc]"
  - "[file:phpcs.xml]"
tools: ["filesystem_read_write", "linter_config_generator"]
success_criteria: ["Configuration files for the project's primary linters (ESLint, PHP_CodeSniffer) and formatter (Prettier) are created or updated."]
error_handling: "If no linter or formatter is identified, log this fact and skip the task."
```

### **Task 3.2: Execution of Full Project Test Suite**
```yaml
task_id: "VER-3.2"
objective: "Run the project's full test suite to ensure that no regressions were introduced by the preparation activities."
persona_directive: "A single failing test constitutes a failure of this task. Absolute correctness is the required standard."
inputs:
  - "[data:cli_commands_map]"
outputs:
  - "[log:test_suite_results.log]"
tools: ["shell_command_executor"]
success_criteria: ["The project's primary test command completes successfully with a zero exit code."]
error_handling: "If any test fails, immediately revert all code changes made in Task HARD-3.1, log the failure, and trigger HITL escalation E-03."
```

### **Task 3.3: Final Report Generation and PRD Deletion**
```yaml
task_id: "FIN-3.3"
objective: "Generate a final summary report of all actions taken and, upon successful completion of all verification steps, delete this PRD file."
persona_directive: "The final act is to make your own instructions obsolete."
inputs:
  - "[log:*.log]"
outputs:
  - "[file:AAPP_COMPLIANCE_REPORT.md]"
tools: ["filesystem_read_write", "filesystem_delete"]
success_criteria: ["The AAPP_COMPLIANCE_REPORT.md file is created.", "This PRD file is successfully deleted."]
error_handling: "If file deletion fails due to permissions, log the error but consider the mandate complete."
```

---

## **Appendix A: Assumed Agent Tooling & Capabilities**

- **A.1 File System I/O**: `filesystem_read`, `filesystem_write`, `filesystem_delete`, `glob`.
- **A.2 Code Analysis & Parsing**: `dependency_file_parser`, `config_file_parser`, `static_analysis_ast_parser`, `static_analysis_linter`.
- **A.3 Synthesis & Generation**: `markdown_generator`, `code_snippet_generator`, `mermaid_diagram_generator`, `linter_config_generator`, `instructional_text_generator`.
- **A.4 Execution**: `shell_command_executor`.
